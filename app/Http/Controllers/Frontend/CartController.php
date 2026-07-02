<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the Shopping Cart page with all active items.
     */
    public function index(): View
    {
        try {
            if (!session()->isStarted()) {
                session()->start();
            }

            // Retrieve all items in current cart (eager loading products and images)
            $cartItems = CartItem::with(['product.images', 'product.category'])
                ->whereHas('cart', function ($q) {
                    if (auth()->check()) {
                        $q->where('user_id', auth()->id());
                    } else {
                        $q->where('session_id', session()->getId());
                    }
                })->get();

            // Calculate subtotal
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item->price_at_time * $item->quantity;
            }

            // Flat rate shipping ₹99, Free shipping if subtotal is above/equal ₹999
            $shippingCharge = $subtotal >= 999 ? 0 : 99;
            $giftWrapCharge = 0; // Toggleable default value (handled on client side too)

        } catch (\Exception $e) {
            Log::error('Cart Page failed to load: ' . $e->getMessage());
            $cartItems = collect();
            $subtotal = 0;
            $shippingCharge = 99;
            $giftWrapCharge = 0;
        }

        return view('pages.cart', compact('cartItems', 'subtotal', 'shippingCharge', 'giftWrapCharge'));
    }

    /**
     * Add an item to the shopping cart (guest session or user ID).
     */
    public function add(Request $request): JsonResponse
    {
        try {
            // Validate incoming payload
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $productId = $request->input('product_id');
            $quantity = (int) $request->input('quantity');

            // Fetch the product
            $product = Product::findOrFail($productId);

            // Fetch or create the cart based on auth status
            if (auth()->check()) {
                $cart = Cart::firstOrCreate([
                    'user_id' => auth()->id()
                ]);
            } else {
                if (!session()->isStarted()) {
                    session()->start();
                }
                $cart = Cart::firstOrCreate([
                    'session_id' => session()->getId()
                ]);
            }

            // Fetch or create cart item for this product
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                // Enforce stock_qty bounds if stock_qty exists
                $newQty = $cartItem->quantity + $quantity;
                if ($product->stock_qty !== null && $newQty > $product->stock_qty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot add more items. Only {$product->stock_qty} items available in stock."
                    ], 422);
                }

                $cartItem->quantity = $newQty;
                $cartItem->save();
            } else {
                // Ensure quantity requested does not exceed stock limits
                if ($product->stock_qty !== null && $quantity > $product->stock_qty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot add {$quantity} items. Only {$product->stock_qty} items available in stock."
                    ], 422);
                }

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_at_time' => $product->sale_price ?? $product->price,
                ]);
            }

            // Retrieve total cart items count
            $cartCount = $cart->cartItems()->sum('quantity');

            return response()->json([
                'success' => true,
                'cart_count' => $cartCount,
                'message' => 'Successfully added to cart'
            ]);

        } catch (\Exception $e) {
            Log::error('Cart Add error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart. Please try again.'
            ], 500);
        }
    }

    /**
     * Update dynamic cart item quantities.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'cart_item_id' => 'required|exists:cart_items,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $itemId = $request->input('cart_item_id');
            $qty = (int) $request->input('quantity');

            $cartItem = CartItem::with(['cart', 'product'])->findOrFail($itemId);
            $cart = $cartItem->cart;

            // Security check - verify cart ownership
            if (!session()->isStarted()) {
                session()->start();
            }
            $isOwner = false;
            if (auth()->check() && $cart->user_id === auth()->id()) {
                $isOwner = true;
            } elseif (!auth()->check() && $cart->session_id === session()->getId()) {
                $isOwner = true;
            }

            if (!$isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action on cart.'
                ], 403);
            }

            // Clamp quantity to product stock limit
            if ($cartItem->product->stock_qty !== null && $qty > $cartItem->product->stock_qty) {
                $qty = $cartItem->product->stock_qty;
            }

            $cartItem->quantity = $qty;
            $cartItem->save();

            // Recalculate totals
            $allItems = CartItem::where('cart_id', $cart->id)->get();
            $subtotal = 0;
            $totalItems = 0;

            foreach ($allItems as $item) {
                $subtotal += $item->price_at_time * $item->quantity;
                $totalItems += $item->quantity;
            }

            $shipping = $subtotal >= 999 ? 0 : 99;
            $total = $subtotal + $shipping;

            return response()->json([
                'success' => true,
                'item_subtotal' => '₹' . number_format($cartItem->price_at_time * $qty, 0),
                'cart_subtotal' => '₹' . number_format($subtotal, 0),
                'cart_shipping' => $shipping === 0 ? 'Free' : '₹' . number_format($shipping, 0),
                'cart_total' => '₹' . number_format($total, 0),
                'cart_count' => $totalItems
            ]);

        } catch (\Exception $e) {
            Log::error('Cart Update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the cart item.'
            ], 500);
        }
    }

    /**
     * Remove an item from the shopping cart.
     */
    public function remove(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'cart_item_id' => 'required|exists:cart_items,id',
            ]);

            $itemId = $request->input('cart_item_id');
            $cartItem = CartItem::with('cart')->findOrFail($itemId);
            $cart = $cartItem->cart;

            // Security check - verify ownership
            if (!session()->isStarted()) {
                session()->start();
            }
            $isOwner = false;
            if (auth()->check() && $cart->user_id === auth()->id()) {
                $isOwner = true;
            } elseif (!auth()->check() && $cart->session_id === session()->getId()) {
                $isOwner = true;
            }

            if (!$isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action on cart.'
                ], 403);
            }

            // Remove cart item
            $cartItem->delete();

            // Recalculate totals
            $allItems = CartItem::where('cart_id', $cart->id)->get();
            $subtotal = 0;
            $totalItems = 0;

            foreach ($allItems as $item) {
                $subtotal += $item->price_at_time * $item->quantity;
                $totalItems += $item->quantity;
            }

            $shipping = $subtotal >= 999 ? 0 : 99;
            $total = $subtotal + $shipping;

            return response()->json([
                'success' => true,
                'cart_subtotal' => '₹' . number_format($subtotal, 0),
                'cart_shipping' => $shipping === 0 ? 'Free' : '₹' . number_format($shipping, 0),
                'cart_total' => '₹' . number_format($total, 0),
                'cart_count' => $totalItems,
                'cart_empty' => $allItems->isEmpty()
            ]);

        } catch (\Exception $e) {
            Log::error('Cart Remove error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing the item.'
            ], 500);
        }
    }

    /**
     * Retrieve the current session/user cart total item count.
     */
    public function count(): JsonResponse
    {
        try {
            $cart = null;

            if (auth()->check()) {
                $cart = Cart::where('user_id', auth()->id())->first();
            } else {
                if (!session()->isStarted()) {
                    session()->start();
                }
                $cart = Cart::where('session_id', session()->getId())->first();
            }

            $count = $cart ? (int) $cart->cartItems()->sum('quantity') : 0;

            return response()->json([
                'success' => true,
                'cart_count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Cart Count error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'cart_count' => 0
            ], 500);
        }
    }
}
