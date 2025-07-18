<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class OrderController extends Controller
{
    // Show the make order to wholesaler page
    public function create()
    {
        $wholesalers = \App\Models\User::role('wholesaler')->get();
        $wholesalerInventories = [];
        foreach ($wholesalers as $wholesaler) {
            $approvedOrderIds = \App\Models\Order::where('wholesaler_id', $wholesaler->id)
                ->where('status', 'approved')
                ->pluck('id');
            $products = \App\Models\Product::whereHas('orderItems', function($q) use ($approvedOrderIds) {
                $q->whereIn('order_id', $approvedOrderIds);
            })->get();
            // Attach wholesaler price if set
            foreach ($products as $product) {
                $wholesalerPrice = \App\Models\WholesalerProductPrice::where('wholesaler_id', $wholesaler->id)
                    ->where('product_id', $product->id)
                    ->value('price');
                $product->wholesaler_price = $wholesalerPrice ?? $product->price;
            }
            $wholesalerInventories[$wholesaler->id] = $products;
        }
        return view('retailer.make_order', compact('wholesalers', 'wholesalerInventories'));
    }

    // Handle order placement (updated for cart)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wholesaler_id' => 'required|exists:users,id',
            'delivery_date' => 'required|date|after_or_equal:today',
            'cart_json' => 'required|string',
        ]);
        $cart = json_decode($validated['cart_json'], true);
        if (!is_array($cart) || count($cart) === 0) {
            return back()->withInput()->withErrors(['cart_json' => 'Please add at least one product to the cart.']);
        }
        $retailerId = auth()->id();
        $total = 0;
        $order = \App\Models\Order::create([
            'order_type' => 'retailer',
            'retailer_id' => $retailerId,
            'wholesaler_id' => $validated['wholesaler_id'],
            'status' => 'pending',
            'order_date' => now(),
            'total_amount' => 0,
            'estimated_delivery_date' => $validated['delivery_date'],
        ]);
        foreach ($cart as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            if ($product) {
                // Get wholesaler price if set
                $wholesalerPrice = \App\Models\WholesalerProductPrice::where('wholesaler_id', $validated['wholesaler_id'])
                    ->where('product_id', $product->id)
                    ->value('price');
                $price = $wholesalerPrice ?? $product->price;
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ]);
                $total += $price * $item['quantity'];
            }
        }
        $order->update(['total_amount' => $total]);
        return redirect()->route('retailer.orders.index')->with('success', 'Order placed successfully!');
    }

    public function index()
    {
        $retailerId = auth()->id();
        $pendingOrders = \App\Models\Order::where('retailer_id', $retailerId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $completedOrders = \App\Models\Order::where('retailer_id', $retailerId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('retailer.orders.index', compact('pendingOrders', 'completedOrders'));
    }

    public function show($orderId)
    {
        $order = \App\Models\Order::with(['factory', 'wholesaler', 'items.product'])->findOrFail($orderId);
        // Optionally, check that the order belongs to the current retailer
        if ($order->retailer_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        return view('retailer.orders.show', compact('order'));
    }

    public function rate($orderId, \Illuminate\Http\Request $request)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        if ($order->retailer_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);
        $order->rating = $validated['rating'];
        $order->feedback = $validated['feedback'] ?? null;
        $order->save();
        return redirect()->route('retailer.orders.show', $order->id)->with('success', 'Thank you for rating your order!');
    }

    public function cancel($orderId, \Illuminate\Http\Request $request)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        if ($order->retailer_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        $order->status = 'cancelled';
        $order->cancelled_reason = $request->input('reason');
        $order->cancelled_at = now();
        $order->save();
        return redirect()->route('retailer.orders.show', $order->id)->with('success', 'Order cancelled successfully.');
    }
}
