<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Retailer\OrderController as RetailerOrderController;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends RetailerOrderController
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:wholesaler']);
    }

    public function index()
    {
        $userId = Auth::id();
        $pendingOrders = Order::where('wholesaler_id', $userId)
            ->where('status', 'pending')
            ->where('order_type', 'wholesaler')
            ->with(['factory', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
        $deliveredOrders = Order::where('wholesaler_id', $userId)
            ->where('status', 'delivered')
            ->where('order_type', 'wholesaler')
            ->with(['factory', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
        $rejectedOrders = Order::where('wholesaler_id', $userId)
            ->where('status', 'rejected')
            ->where('order_type', 'wholesaler')
            ->with(['factory', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        \Log::info('Wholesaler Orders Debug', [
            'user_id' => $userId,
            'pending_count' => $pendingOrders->count(),
            'pending_ids' => $pendingOrders->pluck('id'),
            'delivered_count' => $deliveredOrders->count(),
            'delivered_ids' => $deliveredOrders->pluck('id'),
            'rejected_count' => $rejectedOrders->count(),
            'rejected_ids' => $rejectedOrders->pluck('id'),
            'first_pending_order' => $pendingOrders->first() ? $pendingOrders->first()->toArray() : null,
        ]);
        
        return view('wholesaler.orders.index', compact('pendingOrders', 'deliveredOrders', 'rejectedOrders'));
    }

    public function create()
    {
        $factories = \App\Models\Factory::where('status', true)->get(['id', 'name']);
        return view('wholesaler.orders.create', [
            'factories' => $factories
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Order store() called by user', [
            'user_id' => \Auth::id(),
            'user_email' => \Auth::user()->email,
            'session' => session()->all()
        ]);
        try {
            $validated = $request->validate([
                'factory_id' => 'required|exists:factories,id',
                'products_json' => 'required|string',
                'delivery_address' => 'required|string',
                'delivery_date' => 'required|date|after_or_equal:today',
                'payment_terms' => 'required|string',
                'shipping_method' => 'required|string',
            ]);

            $products = json_decode($validated['products_json'], true);
            if (!is_array($products) || count($products) === 0) {
                return back()->withInput()->withErrors(['products_json' => 'Please add at least one product to the cart.']);
            }

            $total = 0;
            foreach ($products as $productData) {
                $product = \App\Models\Product::find($productData['product_id']);
                if (!$product || $product->factory_id != $validated['factory_id']) {
                    return back()->withInput()->withErrors(['products_json' => 'All products must belong to the selected factory.']);
                }
                $total += $product->price * $productData['quantity'];
            }

            \DB::beginTransaction();
            \Log::info('About to create order', [
                'validated' => $validated,
                'products' => $products,
                'user_id' => \Auth::id()
            ]);
            $wholesalerId = \Auth::id(); // Failsafe: always use authenticated user
            $order = Order::create([
                'order_type' => 'wholesaler',
                'wholesaler_id' => $wholesalerId,
                'factory_id' => $validated['factory_id'],
                'status' => 'pending',
                'delivery_address' => $validated['delivery_address'],
                'estimated_delivery_date' => $validated['delivery_date'],
                'payment_terms' => $validated['payment_terms'],
                'shipping_method' => $validated['shipping_method'],
                'created_by' => $wholesalerId,
                'total_amount' => $total,
                'order_date' => now(),
            ]);

            foreach ($products as $productData) {
                $product = \App\Models\Product::find($productData['product_id']);
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'price' => $product->price, // Fix: use 'price' field as required by DB
                ]);
            }

            // Broadcast order status update
            event(new \App\Events\OrderStatusUpdated($order));

            \DB::commit();

            \Log::info('Order created', $order->toArray());

            \Log::info('Order created', [
                'order_id' => $order->id,
                'factory_id' => $order->factory_id,
                'order_type' => $order->order_type,
                'status' => $order->status,
                'wholesaler_id' => $order->wholesaler_id,
            ]);

            // Notify factory
            if ($order->factory && $order->factory->user) {
                try {
                    $order->factory->user->notify(new \App\Notifications\OrderStatusChangedNotification(
                        $order, 
                        'New Wholesale Order Created'
                    ));
                } catch (\Exception $e) {
                    \Log::error("Failed to send notification: " . $e->getMessage());
                }
            }

            return redirect()->route('wholesaler.orders.index')
                ->with('success', 'Order created successfully. Awaiting factory approval.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function show($orderId)
    {
        $order = \App\Models\Order::with(['items.product', 'factory'])->findOrFail($orderId);
        if ($order->wholesaler_id !== \Auth::id()) {
            return redirect()->route('wholesaler.orders.index')
                ->with('error', 'You do not have permission to view this order.');
        }
        return view('wholesaler.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        if ($order->wholesaler_id !== Auth::id() || $order->status) {
            return redirect()->route('wholesaler.orders.index')
                ->with('error', 'You cannot update this order.');
        }

        $validated = $request->validate([
            'delivery_address' => 'required|string',
            'delivery_date' => 'required|date|after_or_equal:today',
            'wholesale_price' => 'required|numeric|min:0',
            'payment_terms' => 'required|string',
            'shipping_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('wholesaler.orders.show', $order)
            ->with('success', 'Order details updated successfully.');
    }

    public function cancel($orderId, \Illuminate\Http\Request $request)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        if ($order->wholesaler_id !== \Auth::id() || $order->status) {
            return redirect()->route('wholesaler.orders.index')
                ->with('error', 'You cannot cancel this order.');
        }
        $order->update([
            'status' => false,
            'cancelled_at' => now(),
            'cancelled_by' => \Auth::id(),
            'cancelled_reason' => $request->input('reason') ?? 'Cancelled by wholesaler'
        ]);
        // Notify factory
        if ($order->factory && $order->factory->user) {
            $order->factory->user->notify(new \App\Notifications\OrderStatusChangedNotification(
                $order,
                'Wholesale Order Cancelled'
            ));
        }
        return redirect()->route('wholesaler.orders.index')
            ->with('success', 'Order cancelled successfully.');
    }

    public function rate($orderId, \Illuminate\Http\Request $request)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        if ($order->wholesaler_id !== \Auth::id() || !$order->status) {
            return redirect()->route('wholesaler.orders.index')
                ->with('error', 'You cannot rate this order.');
        }
        $validated = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
            'volume_feedback' => 'required|string', // Specific to wholesalers
            'price_feedback' => 'required|string',  // Specific to wholesalers
        ]);
        $order->update([
            'rating' => $validated['rating'],
            'feedback' => $validated['feedback'] ?? null,
            'volume_feedback' => $validated['volume_feedback'],
            'price_feedback' => $validated['price_feedback']
        ]);
        return redirect()->route('wholesaler.orders.show', $order->id)
            ->with('success', 'Order rated successfully.');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $products = Product::where('wholesaler_id', $user->id)->get();
        $totalStock = $products->sum('stock');

        $acceptedOrderIds = Order::where('wholesaler_id', $user->id)
            ->where('status', true)
            ->pluck('id');
        $totalAccepted = \App\Models\OrderItem::whereIn('order_id', $acceptedOrderIds)->sum('quantity');

        $retailerOrderIds = Order::where('order_type', 'retailer')
            ->where('wholesaler_id', $user->id)
            ->pluck('id');
        $totalSoldToRetailers = \App\Models\OrderItem::whereIn('order_id', $retailerOrderIds)->sum('quantity');

        return view('wholesaler.dashboard', compact('products', 'totalStock', 'totalAccepted', 'totalSoldToRetailers'));
    }

    public function getFactoryProducts($factoryId)
    {
        $products = \App\Models\Product::where('factory_id', $factoryId)
            ->where('status', true)
            ->get(['id', 'name', 'price', 'stock']);
        return response()->json($products);
    }
}
