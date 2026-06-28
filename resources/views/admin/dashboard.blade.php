@extends('layouts.admin')

@section('content')
<div class="flex flex-col gap-10">
    
    <!-- Header -->
    <div class="border-b border-gold-accent/10 pb-6">
        <h1 class="font-serif text-3xl text-gold-accent">Admin Dashboard</h1>
        <p class="text-xs uppercase tracking-wider text-muted-content mt-1">
            Overview of KD Artisan Room metrics and operations
        </p>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Orders -->
        <div class="border border-gold-accent/10 p-6 rounded-sm bg-[#070b09] flex flex-col gap-2">
            <span class="text-[9px] uppercase tracking-wider text-muted-content font-sans">Total Orders</span>
            <div class="flex justify-between items-baseline">
                <span class="text-3xl font-sans font-bold text-text-content">{{ $totalOrdersCount }}</span>
                <i class="ph ph-shopping-bag text-gold-accent/30 text-2xl"></i>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="border border-gold-accent/10 p-6 rounded-sm bg-[#070b09] flex flex-col gap-2">
            <span class="text-[9px] uppercase tracking-wider text-muted-content font-sans">Total Revenue</span>
            <div class="flex justify-between items-baseline">
                <span class="text-2xl font-sans font-bold text-gold-accent">₹{{ number_format($totalRevenue, 0) }}</span>
                <i class="ph ph-currency-inr text-gold-accent/30 text-2xl"></i>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="border border-gold-accent/10 p-6 rounded-sm bg-[#070b09] flex flex-col gap-2">
            <span class="text-[9px] uppercase tracking-wider text-muted-content font-sans">Pending Orders</span>
            <div class="flex justify-between items-baseline">
                <span class="text-3xl font-sans font-bold text-warm-amber">{{ $pendingOrdersCount }}</span>
                <i class="ph ph-clock-counter-clockwise text-gold-accent/30 text-2xl"></i>
            </div>
        </div>

        <!-- Total Products -->
        <div class="border border-gold-accent/10 p-6 rounded-sm bg-[#070b09] flex flex-col gap-2">
            <span class="text-[9px] uppercase tracking-wider text-muted-content font-sans">Total Products</span>
            <div class="flex justify-between items-baseline">
                <span class="text-3xl font-sans font-bold text-text-content">{{ $totalProductsCount }}</span>
                <i class="ph ph-sketch-logo text-gold-accent/30 text-2xl"></i>
            </div>
        </div>

    </div>

    <!-- Recent Orders Table -->
    <div class="border border-gold-accent/10 rounded-sm bg-[#070b09] p-6">
        <h2 class="font-serif text-lg text-gold-accent border-b border-gold-accent/10 pb-4 mb-6">
            Recent Orders
        </h2>

        @if($recentOrders->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs font-sans">
                    <thead>
                        <tr class="border-b border-gold-accent/10 text-muted-content uppercase tracking-wider">
                            <th class="py-3 px-4 font-semibold">Order Number</th>
                            <th class="py-3 px-4 font-semibold">Customer</th>
                            <th class="py-3 px-4 font-semibold">Total</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr class="border-b border-gold-accent/5 hover:bg-gold-accent/5 transition-colors duration-200">
                                <td class="py-4 px-4 font-semibold text-gold-accent">{{ $order->order_number }}</td>
                                <td class="py-4 px-4">{{ $order->name }}</td>
                                <td class="py-4 px-4">₹{{ number_format($order->total_amount, 0) }}</td>
                                <td class="py-4 px-4">
                                    @if($order->status === 'pending')
                                        <span class="bg-warm-amber/15 text-warm-amber text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-sm">Pending</span>
                                    @elseif($order->status === 'confirmed')
                                        <span class="bg-sky-500/15 text-sky-400 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-sm">Confirmed</span>
                                    @elseif($order->status === 'shipped')
                                        <span class="bg-emerald-500/15 text-emerald-400 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-sm">Shipped</span>
                                    @elseif($order->status === 'delivered')
                                        <span class="bg-green-600/15 text-green-500 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-sm">Delivered</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="bg-rose-500/15 text-rose-400 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-sm">Cancelled</span>
                                    @else
                                        <span class="bg-muted-content/15 text-muted-content text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-sm">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-muted-content">{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 text-muted-content italic">
                No orders placed yet.
            </div>
        @endif
    </div>

</div>
@endsection
