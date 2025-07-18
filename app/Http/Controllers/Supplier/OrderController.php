<?php

namespace App\Http\Controllers\Supplier;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    public function index()
    {
        $supplier = Auth::user();
        // Pending orders: status = 'pending', false, or 0
        $pendingOrders = Order::where('supplier_id', $supplier->id)
            ->where(function($q) {
                $q->where('status', 'pending')
                  ->orWhere('status', false)
                  ->orWhere('status', 0);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        // Completed orders: status = 'accepted', 'delivered', true, or 1
        $completedOrders = Order::where('supplier_id', $supplier->id)
            ->where(function($q) {
                $q->whereIn('status', ['accepted', 'delivered'])
                  ->orWhere('status', true)
                  ->orWhere('status', 1);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate current month revenue
        $completedOrderIds = Order::where('supplier_id', $supplier->id)
            ->where('status', true)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->pluck('id');

        $currentMonthRevenue = OrderItem::whereIn('order_id', $completedOrderIds)
            ->sum(\DB::raw('quantity * price'));

        // Calculate average delivery time (in days) for completed orders
        $averageDeliveryTime = Order::where('supplier_id', $supplier->id)
            ->where('status', true)
            ->whereNotNull('delivered_at')
            ->get()
            ->map(function($order) {
                return $order->delivered_at && $order->created_at
                    ? $order->delivered_at->diffInDays($order->created_at)
                    : null;
            })
            ->filter()
            ->avg() ?? 0;

        return view('supplier.orders.index', compact('pendingOrders', 'completedOrders', 'currentMonthRevenue', 'averageDeliveryTime'));
    }

    public function show(Order $order)
    {
        $supplier = Auth::user();
        
        if ($order->supplier_id !== $supplier->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('supplier.orders.show', compact('order'));
    }

    public function accept(Request $request, Order $order)
    {
        $supplier = Auth::user();
        if ($order->supplier_id !== $supplier->id || $order->status) {
            $message = 'Invalid order or already processed';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $order->update([
            'status' => true,
            'supplier_accepted_at' => now(),
            'supplier_accepted_by' => Auth::id()
        ]);

        // Notify factory
        if ($order->factory && $order->factory->user) {
            $order->factory->user->notify(new \App\Notifications\OrderStatusChangedNotification(
                $order, 
                'Supplier Accepted'
            ));
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Order accepted successfully.']);
        }
        return redirect()->route('supplier.orders.index')
            ->with('success', 'Order accepted successfully.');
    }

    public function reject(Request $request, Order $order)
    {
        $supplier = Auth::user();
        if ($order->supplier_id !== $supplier->id || $order->status) {
            $message = 'Invalid order or already processed';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $order->update([
            'status' => false,
            'supplier_rejected_at' => now(),
            'supplier_rejected_by' => Auth::id()
        ]);

        // Notify factory
        if ($order->factory && $order->factory->user) {
            $order->factory->user->notify(new \App\Notifications\OrderStatusChangedNotification(
                $order, 
                'Supplier Rejected'
            ));
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Order rejected successfully.']);
        }
        return redirect()->route('supplier.orders.index')
            ->with('success', 'Order rejected successfully.');
    }

    public function updateDeliveryStatus(Order $order)
    {
        $supplier = Auth::user();
        
        if ($order->supplier_id !== $supplier->id || !$order->status) {
            return redirect()->back()->with('error', 'Invalid order or not accepted yet');
        }

        $validated = request()->validate([
            'delivery_status' => 'required|string|in:preparing,shipped,delivered',
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $order->update([
            'delivery_status' => $validated['delivery_status'],
            'tracking_number' => $validated['tracking_number'] ?? null,
            'supplier_notes' => $validated['notes'] ?? null,
            'updated_by' => Auth::id()
        ]);

        // Notify factory
        $order->factory->user->notify(new \App\Notifications\OrderStatusChangedNotification(
            $order, 
            'Delivery Status Updated: ' . ucfirst($validated['delivery_status'])
        ));

        return redirect()->route('supplier.orders.show', $order)
            ->with('success', 'Delivery status updated successfully.');
    }

    public function qualityCheck(Order $order)
    {
        $supplier = Auth::user();
        
        if ($order->supplier_id !== $supplier->id || !$order->status) {
            return redirect()->back()->with('error', 'Invalid order or not accepted yet');
        }

        $validated = request()->validate([
            'items.*.id' => 'required|exists:order_items,id',
            'items.*.quality_grade' => 'required|string|in:A,B,C,D',
            'items.*.notes' => 'nullable|string',
        ]);

        foreach ($validated['items'] as $itemData) {
            $item = OrderItem::find($itemData['id']);
            $item->update([
                'quality_grade' => $itemData['quality_grade'],
                'supplier_notes' => $itemData['notes'] ?? null,
                'quality_checked_at' => now(),
                'quality_checked_by' => Auth::id()
            ]);
        }

        return redirect()->route('supplier.orders.show', $order)
            ->with('success', 'Quality check completed successfully.');
    }
}
