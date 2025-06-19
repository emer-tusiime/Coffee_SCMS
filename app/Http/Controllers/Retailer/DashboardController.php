<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:retailer']);
    }

    public function index()
    {
        $user = Auth::user();

        // Get retailer's orders
        $orders = Order::where('retailer_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Calculate statistics
        $stats = [
            'total_orders' => Order::where('retailer_id', $user->id)->count(),
            'pending_orders' => Order::where('retailer_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'completed_orders' => Order::where('retailer_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'total_products' => Product::count(), // Total available products
        ];

        // Get low stock products (you can customize the threshold)
        $lowStockProducts = Product::where('stock', '<', 10)->get();

        return view('retailer.dashboard', compact('orders', 'stats', 'lowStockProducts'));
    }
}
