<?php

namespace App\Http\Controllers\Factory;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:factory']);
    }

    public function index(Request $request)
    {
        $factory = Auth::user()->factory;
        
        // Base query for orders
        $query = Order::where('factory_id', $factory->id)
            ->with(['wholesaler', 'items.product']);
            
        // Apply filters if any
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get orders with pagination
        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get orders by status
        $pendingOrders = Order::where('factory_id', $factory->id)
            ->where('status', 'pending')
            ->where('order_type', 'wholesaler')
            ->with(['wholesaler', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $rejectedOrders = Order::where('factory_id', $factory->id)
            ->where('status', 'rejected')
            ->where('order_type', 'wholesaler')
            ->with(['wholesaler', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $deliveredOrders = Order::where('factory_id', $factory->id)
            ->where('status', 'delivered')
            ->where('order_type', 'wholesaler')
            ->with(['wholesaler', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Orders to suppliers (new)
        $supplierPendingOrders = Order::where('factory_id', $factory->id)
            ->where('status', 'pending')
            ->where('order_type', 'supplier')
            ->with(['supplier', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
        $supplierRejectedOrders = Order::where('factory_id', $factory->id)
            ->where('status', 'rejected')
            ->where('order_type', 'supplier')
            ->with(['supplier', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
        $supplierDeliveredOrders = Order::where('factory_id', $factory->id)
            ->where('status', 'delivered')
            ->where('order_type', 'supplier')
            ->with(['supplier', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch all approved suppliers and their products
        $suppliersWithProducts = \App\Models\User::where('role', 'supplier')
            ->where('approved', true)
            ->with(['products' => function($query) {
                $query->where('status', true);
            }])
            ->get();

        // Get order statistics
        $stats = [
            'total' => Order::where('factory_id', $factory->id)->count(),
            'pending' => $pendingOrders->count(),
            'rejected' => $rejectedOrders->count(),
            'delivered' => $deliveredOrders->count(),
        ];
        
        return view('factory.orders.index', [
            'orders' => $orders,
            'pendingOrders' => $pendingOrders,
            'rejectedOrders' => $rejectedOrders,
            'deliveredOrders' => $deliveredOrders,
            'supplierPendingOrders' => $supplierPendingOrders,
            'supplierRejectedOrders' => $supplierRejectedOrders,
            'supplierDeliveredOrders' => $supplierDeliveredOrders,
            'suppliersWithProducts' => $suppliersWithProducts,
            'stats' => $stats,
            'filters' => $request->all()
        ]);
    }

    public function create()
    {
        // Show all approved suppliers
        $suppliers = \App\Models\User::where('role', 'supplier')->where('approved', true)->get();
        return view('factory.orders.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        // Support both single and multiple product order forms
        if ($request->has('product_id')) {
            // Single product order (from supplier detail view)
            $validated = $request->validate([
                'supplier_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);
            // Ensure supplier is valid and approved
            $supplier = User::where('id', $validated['supplier_id'])->where('role', 'supplier')->where('approved', true)->first();
            if (!$supplier) {
                return back()->withErrors(['supplier_id' => 'Selected supplier is not valid or not approved.'])->withInput();
            }
            $factory = Auth::user()->factory;
            $product = Product::find($validated['product_id']);
            $total = $product->price * $validated['quantity'];
            $order = Order::create([
                'order_type' => 'supplier',
                'supplier_id' => $supplier->id,
                'factory_id' => $factory->id,
                'status' => 'pending',
                'created_by' => Auth::id(),
                'estimated_delivery_date' => now()->addDays(7),
                'order_date' => now(),
                'total_amount' => $total,
            ]);
            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->price,
            ]);
            $product->decrement('stock', $validated['quantity']);
            $supplier->notify(new \App\Notifications\NewOrderNotification($order));
        } else {
            // Accept products_json from the cart and decode it
            $products = json_decode($request->input('products_json'), true);
            $request->merge(['products' => $products]);
            $validated = $request->validate([
                'supplier_id' => 'required|exists:users,id',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|numeric|min:1',
            ]);
            // Ensure supplier is valid and approved
            $supplier = User::where('id', $validated['supplier_id'])->where('role', 'supplier')->where('approved', true)->first();
            if (!$supplier) {
                return back()->withErrors(['supplier_id' => 'Selected supplier is not valid or not approved.'])->withInput();
            }
            $factory = Auth::user()->factory;
            $total = 0;
            foreach ($validated['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $total += $product->price * $productData['quantity'];
            }
            $order = Order::create([
                'order_type' => 'supplier',
                'supplier_id' => $supplier->id,
                'factory_id' => $factory->id,
                'status' => 'pending',
                'created_by' => Auth::id(),
                'estimated_delivery_date' => $request->input('estimated_delivery_date', now()->addDays(7)),
                'order_date' => now(),
                'total_amount' => $total,
            ]);
            foreach ($validated['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'price' => $product->price,
                ]);
                $product->decrement('stock', $productData['quantity']);
            }
            $supplier->notify(new \App\Notifications\NewOrderNotification($order));
        }
        return redirect()->route('factory.orders.index')
            ->with('success', 'Order created successfully. Awaiting supplier approval.');
    }

    public function show(Order $order)
    {
        $factory = Auth::user()->factory;
        
        // Verify the order belongs to this factory
        if ($order->factory_id !== $factory->id) {
            return redirect()->route('factory.orders.index')
                ->with('error', 'You do not have permission to view this order.');
        }
        
        // Get order with related data
        $order->load([
            'wholesaler',
            'items.product',
            // 'statusHistory' => function($query) {
            //     $query->orderBy('created_at', 'desc');
            // },
            // 'delivery' => function($query) {
            //     $query->with('deliveryPerson');
            // }
        ]);
        
        // Get related products that could be suggested
        $suggestedProducts = Product::where('factory_id', $factory->id)
            ->where('status', true)
            ->whereNotIn('id', $order->items->pluck('product_id'))
            ->inRandomOrder()
            ->take(4)
            ->get();
        
        return view('factory.orders.show', [
            'order' => $order,
            'suggestedProducts' => $suggestedProducts
        ]);
    }

    public function approve(Order $order)
    {
        $factory = Auth::user()->factory;
        if ($order->factory_id !== $factory->id || $order->status !== 'pending') {
            return redirect()->back()->with('error', 'Invalid order or already processed');
        }
        $order->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);
        // Broadcast order status update
        event(new \App\Events\OrderStatusUpdated($order));
        // Notify the correct user
        if ($order->order_type === 'wholesaler' && $order->wholesaler) {
            $order->wholesaler->notify(new \App\Notifications\OrderStatusChangedNotification(
                $order, 
                'Approved'
            ));
        } elseif ($order->order_type === 'supplier' && $order->supplier) {
            $order->supplier->notify(new \App\Notifications\OrderStatusChangedNotification(
                $order, 
                'Approved'
            ));
        }
        return redirect()->route('factory.orders.index')
            ->with('success', 'Order approved successfully.');
    }

    public function reject(Order $order)
    {
        $factory = Auth::user()->factory;
        if ($order->factory_id !== $factory->id || $order->status !== 'pending') {
            return redirect()->back()->with('error', 'Invalid order or already processed');
        }
        // Notify the correct user before updating
        if ($order->order_type === 'wholesaler' && $order->wholesaler) {
            $order->wholesaler->notify(new \App\Notifications\OrderStatusChangedNotification(
                $order, 
                'Rejected'
            ));
        } elseif ($order->order_type === 'supplier' && $order->supplier) {
            $order->supplier->notify(new \App\Notifications\OrderStatusChangedNotification(
                $order, 
                'Rejected'
            ));
        }
        $order->update(['status' => 'rejected']);
        // Broadcast order status update
        event(new \App\Events\OrderStatusUpdated($order));
        return redirect()->route('factory.orders.index')
            ->with('success', 'Order rejected successfully.');
    }

    public function updateStatus(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $factory = Auth::user()->factory;
        
        // Verify the order belongs to this factory
        if ($order->factory_id !== $factory->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this order.'
            ], 403);
        }
        
        // Check valid status transitions
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
        ];
        
        if (isset($validTransitions[$order->status]) && 
            !in_array($request->status, $validTransitions[$order->status])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status transition.'
            ], 422);
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Update order status
            $order->status = $request->status;
            
            // Set timestamps based on status
            switch ($request->status) {
                case 'processing':
                    $order->processing_at = now();
                    break;
                case 'shipped':
                    $order->shipped_at = now();
                    break;
                case 'delivered':
                    $order->delivered_at = now();
                    $order->payment_status = 'paid';
                    break;
                case 'cancelled':
                    // Restore product stock if cancelled
                    foreach ($order->items as $item) {
                        if ($item->product) {
                            $item->product->increment('stock', $item->quantity);
                        }
                    }
                    $order->cancelled_at = now();
                    break;
            }
            
            $order->save();
            
            // Add status history
            $order->statusHistory()->create([
                'status' => $request->status,
                'notes' => $request->notes,
                'changed_by' => Auth::id()
            ]);
            
            // Notify wholesaler about status change
            if ($order->wholesaler) {
                $order->wholesaler->notify(new \App\Notifications\OrderStatusUpdated(
                    $order, 
                    $request->status,
                    $request->notes
                ));
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.',
                'status' => $request->status,
                'status_label' => ucfirst($request->status),
                'updated_at' => now()->format('M d, Y h:i A')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating order status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status. Please try again.'
            ], 500);
        }
    }

    public function getSupplierProducts($supplierId)
    {
        $products = \App\Models\Product::where('supplier_id', $supplierId)
            ->where('status', true)
            ->get(['id', 'name', 'price', 'stock', 'quality_grade']);
        return response()->json($products);
    }

    public function wholesalerOrders()
    {
        $factory = auth()->user()->factory;
        $orders = \App\Models\Order::where('factory_id', $factory->id)
            ->where('order_type', 'wholesaler')
            ->with(['wholesaler', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('factory.orders.wholesaler_orders', compact('orders'));
    }
}
