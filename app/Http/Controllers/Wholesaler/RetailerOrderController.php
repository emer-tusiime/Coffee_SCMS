<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RetailerOrderController extends Controller
{
    public function index()
    {
        $retailerOrders = \App\Models\Order::where('order_type', 'retailer')
            ->where('wholesaler_id', auth()->id())
            ->with(['retailer', 'products'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('wholesaler.retailer_orders.index', compact('retailerOrders'));
    }

    public function approve($orderId)
    {
        $order = \App\Models\Order::where('order_type', 'retailer')
            ->where('wholesaler_id', auth()->id())
            ->where('id', $orderId)
            ->firstOrFail();
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Order is not pending.');
        }
        $order->status = 'approved';
        $order->save();
        return redirect()->back()->with('success', 'Order approved successfully.');
    }

    public function reject($orderId, Request $request)
    {
        $order = \App\Models\Order::where('order_type', 'retailer')
            ->where('wholesaler_id', auth()->id())
            ->where('id', $orderId)
            ->firstOrFail();
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Order is not pending.');
        }
        $order->status = 'rejected';
        $order->cancelled_reason = $request->input('reason');
        $order->cancelled_at = now();
        $order->save();
        return redirect()->back()->with('success', 'Order rejected successfully.');
    }

    public function show($orderId)
    {
        $order = \App\Models\Order::where('order_type', 'retailer')
            ->where('wholesaler_id', auth()->id())
            ->with(['retailer', 'items.product'])
            ->findOrFail($orderId);
        return view('wholesaler.retailer_orders.show', compact('order'));
    }
} 