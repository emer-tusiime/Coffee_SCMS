<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:retailer']);
    }

    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        $orders = Order::where('retailer_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('retailer.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Ensure the order belongs to the retailer
        if ($order->retailer_id !== Auth::id()) {
            return redirect()->route('retailer.orders.index')
                ->with('error', 'You do not have permission to view this order.');
        }

        $order->load(['items.product']);
        return view('retailer.orders.show', compact('order'));
    }
}
