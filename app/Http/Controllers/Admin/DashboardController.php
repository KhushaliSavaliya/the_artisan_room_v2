<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard with metrics and recent orders.
     */
    public function index(): View
    {
        // 1. Metric Counts
        $totalOrdersCount = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $totalProductsCount = Product::count();

        // 2. 5 Most Recent Orders
        $recentOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalOrdersCount',
            'totalRevenue',
            'pendingOrdersCount',
            'totalProductsCount',
            'recentOrders'
        ));
    }
}
