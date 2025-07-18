<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $wholesalerId = Auth::id();

        // Fetch all products with their factories (only needed fields)
        $products = Product::with(['factory' => function($q) {
            $q->select('id', 'name');
        }])
        ->whereNotNull('factory_id')
        ->get(['id','name','description','price','factory_id','stock']);

        // Fetch orders for this wholesaler by status
        $pendingOrders = Order::where('wholesaler_id', $wholesalerId)
            ->where('order_type', 'wholesaler')
            ->where('status', 'pending')
            ->with(['factory', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedOrders = Order::where('wholesaler_id', $wholesalerId)
            ->where('order_type', 'wholesaler')
            ->where('status', 'approved')
            ->with(['factory', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $rejectedOrders = Order::where('wholesaler_id', $wholesalerId)
            ->where('order_type', 'wholesaler')
            ->where('status', 'rejected')
            ->with(['factory', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $deliveredOrders = Order::where('wholesaler_id', $wholesalerId)
            ->where('order_type', 'wholesaler')
            ->where('status', 'delivered')
            ->with(['factory', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Recent orders (any status)
        $recentOrders = Order::where('wholesaler_id', $wholesalerId)
            ->where('order_type', 'wholesaler')
            ->with(['factory', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Total products in stock (from delivered and approved orders)
        $deliveredAndApprovedOrderIds = $deliveredOrders->pluck('id')->merge($approvedOrders->pluck('id'));
        $totalStock = \App\Models\OrderItem::whereIn('order_id', $deliveredAndApprovedOrderIds)->sum('quantity');

        $debug = 'DashboardController: Data loaded and passed to view.';

        return view('wholesaler.dashboard', compact(
            'products',
            'pendingOrders',
            'approvedOrders',
            'rejectedOrders',
            'deliveredOrders',
            'recentOrders',
            'debug',
            'totalStock'
        ));
    }
} 