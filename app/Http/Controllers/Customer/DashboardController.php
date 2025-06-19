<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    public function index()
    {
        $user = Auth::user();

        // Get customer's orders
        $orders = Order::where('customer_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Get order statistics
        $orderStats = [
            'total' => Order::where('customer_id', $user->id)->count(),
            'pending' => Order::where('customer_id', $user->id)->where('status', 'pending')->count(),
            'processing' => Order::where('customer_id', $user->id)->where('status', 'processing')->count(),
            'completed' => Order::where('customer_id', $user->id)->where('status', 'completed')->count()
        ];

        return view('customer.dashboard', compact('orders', 'orderStats'));
    }
}
