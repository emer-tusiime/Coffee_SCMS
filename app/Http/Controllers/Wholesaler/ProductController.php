<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WholesalerProductPrice;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $wholesalerId = auth()->id();
        // Get all approved orders for this wholesaler
        $approvedOrders = \App\Models\Order::where('wholesaler_id', $wholesalerId)
            ->where('status', 'approved')
            ->with(['items.product.factory'])
            ->get();

        // Collect all products from these orders, including quantity and order info
        $products = collect();
        foreach ($approvedOrders as $order) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $products->push([
                        'name' => $product->name,
                        'factory' => $product->factory->name ?? 'N/A',
                        'price' => $product->price,
                        'quantity' => $item->quantity,
                        'order_id' => $order->id,
                        'order_date' => $order->order_date,
                    ]);
                }
            }
        }

        return view('wholesaler.products.index', compact('products'));
    }

    public function editPrices()
    {
        $wholesalerId = auth()->id();
        $approvedOrders = \App\Models\Order::where('wholesaler_id', $wholesalerId)
            ->where('status', 'approved')
            ->with(['items.product.factory'])
            ->get();
        $products = collect();
        foreach ($approvedOrders as $order) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $products->put($product->id, [
                        'id' => $product->id,
                        'name' => $product->name,
                        'factory' => $product->factory->name ?? 'N/A',
                        'default_price' => $product->price,
                        'wholesaler_price' => WholesalerProductPrice::where('wholesaler_id', $wholesalerId)->where('product_id', $product->id)->value('price'),
                    ]);
                }
            }
        }
        return view('wholesaler.products.edit', ['products' => $products->values()]);
    }

    public function updatePrices(Request $request)
    {
        $wholesalerId = auth()->id();
        $data = $request->validate([
            'prices' => 'required|array',
            'prices.*.product_id' => 'required|integer|exists:products,id',
            'prices.*.price' => 'required|numeric|min:0',
        ]);
        foreach ($data['prices'] as $priceData) {
            WholesalerProductPrice::updateOrCreate(
                [
                    'wholesaler_id' => $wholesalerId,
                    'product_id' => $priceData['product_id'],
                ],
                [
                    'price' => $priceData['price'],
                ]
            );
        }
        return redirect()->route('wholesaler.products.edit')->with('success', 'Prices updated successfully.');
    }
} 