<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\Order;

class WholesalerController extends Controller
{
    // Show all wholesalers for selection
    public function selectWholesaler()
    {
        $wholesalers = User::role('wholesaler')->get();
        return view('retailer.select_wholesaler', compact('wholesalers'));
    }

    // Show products for the selected wholesaler
    public function wholesalerProducts($wholesalerId)
    {
        $wholesaler = User::findOrFail($wholesalerId);
        $products = $wholesaler->products; // Assumes User model has products() relationship
        return view('retailer.wholesaler_products', compact('wholesaler', 'products'));
    }

    // Add product to cart
    public function addToCart(Request $request, $wholesalerId)
    {
        $productId = $request->input('product_id');
        $quantity = max(1, (int)$request->input('quantity', 1));
        $cart = Session::get('retailer_cart', []);

        // Key by wholesaler to allow multiple carts if needed
        if (!isset($cart[$wholesalerId])) {
            $cart[$wholesalerId] = [];
        }
        if (isset($cart[$wholesalerId][$productId])) {
            $cart[$wholesalerId][$productId]['quantity'] += $quantity;
        } else {
            $cart[$wholesalerId][$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }
        Session::put('retailer_cart', $cart);
        return back()->with('success', 'Product added to cart!');
    }

    // View cart
    public function viewCart($wholesalerId)
    {
        $cart = Session::get('retailer_cart', []);
        $items = $cart[$wholesalerId] ?? [];
        $products = Product::whereIn('id', array_keys($items))->get();
        return view('retailer.cart', compact('products', 'items', 'wholesalerId'));
    }

    // Place order
    public function placeOrder(Request $request, $wholesalerId)
    {
        $cart = Session::get('retailer_cart', []);
        $items = $cart[$wholesalerId] ?? [];
        if (empty($items)) {
            return back()->with('error', 'Your cart is empty.');
        }
        $retailerId = auth()->id();
        $total = 0;
        $order = Order::create([
            'order_type' => 'retailer',
            'retailer_id' => $retailerId,
            'wholesaler_id' => $wholesalerId,
            'status' => 'pending',
            'order_date' => now(),
            'total_amount' => 0, // Will update after items
        ]);
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
                $total += $product->price * $item['quantity'];
            }
        }
        $order->update(['total_amount' => $total]);
        // Clear cart after order
        unset($cart[$wholesalerId]);
        Session::put('retailer_cart', $cart);
        return redirect()->route('retailer.orders.index')->with('success', 'Order placed successfully!');
    }
} 