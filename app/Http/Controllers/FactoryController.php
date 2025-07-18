<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FactoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:factory']);
    }

    public function dashboard()
    {
        $factory = Auth::user()->factory;
        
        $stats = [
            'total_products' => $factory->products->count(),
            'active_products' => $factory->products->where('status', true)->count(),
            'pending_orders' => Order::where('factory_id', $factory->id)
                ->where('status', false)
                ->count(),
            'total_orders' => Order::where('factory_id', $factory->id)
                ->count(),
            'supplier_count' => $factory->suppliers->count(),
            'recent_orders' => Order::where('factory_id', $factory->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
        ];

        return view('factory.dashboard', compact('stats'));
    }

    public function suppliers()
    {
        $factory = Auth::user()->factory;
        $suppliers = $factory->suppliers;
        
        return view('factory.suppliers.index', compact('suppliers'));
    }

    public function products()
    {
        $factory = Auth::user()->factory;

        if (!$factory) {
            // Show empty products and a message if no factory profile exists
            return view('factory.products.index', [
                'products' => collect(),
                'factoryExists' => false
            ]);
        }

        $products = $factory->products;
        return view('factory.products.index', [
            'products' => $products,
            'factoryExists' => true
        ]);
    }

    public function orders()
    {
        $factory = Auth::user()->factory;
        $pendingOrders = Order::where('factory_id', $factory->id)
            ->where('status', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $completedOrders = Order::where('factory_id', $factory->id)
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return view('factory.orders.index', compact('pendingOrders', 'completedOrders'));
    }

    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0',
        ]);

        $factory = Auth::user()->factory;
        
        $order = Order::create([
            'order_type' => 'supplier',
            'supplier_id' => $validated['supplier_id'],
            'factory_id' => $factory->id,
            'status' => false,
            'created_by' => Auth::id(),
            'estimated_delivery_date' => now()->addDays(7), // Default 7 days
        ]);

        foreach ($validated['products'] as $productData) {
            $product = Product::find($productData['product_id']);
            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'unit_price' => $product->price,
                'total' => $product->price * $productData['quantity'],
            ]);
        }

        return redirect()->route('factory.orders.index')
            ->with('success', 'Order created successfully. Awaiting supplier approval.');
    }
}
