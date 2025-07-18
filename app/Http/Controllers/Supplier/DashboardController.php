<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ChatMessage;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Factory;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    public function index()
    {
        $supplierId = Auth::id();

        // All products for this supplier
        $products = Product::where('supplier_id', $supplierId)->get();

        // Orders from factories to this supplier
        $pendingOrders = Order::where('supplier_id', $supplierId)
            ->where('order_type', 'supplier')
            ->where(function($q) {
                $q->where('status', 'pending')
                  ->orWhere('status', false)
                  ->orWhere('status', 0);
            })
            ->with(['factory', 'products'])
            ->get();

        $deliveredOrders = Order::where('supplier_id', $supplierId)
            ->where('order_type', 'supplier')
            ->where(function($q) {
                $q->whereIn('status', ['accepted', 'delivered'])
                  ->orWhere('status', true)
                  ->orWhere('status', 1);
            })
            ->with(['factory', 'products'])
            ->get();

        $allOrders = Order::where('supplier_id', $supplierId)
            ->where('order_type', 'supplier')
            ->with(['factory', 'products'])
            ->get();

        $factories = Factory::all();

        // Get counts for overview cards
        $totalProducts = Product::where('supplier_id', $supplierId)->count();
        $pendingOrdersCount = $pendingOrders->count();
        $lowStockItems = Inventory::whereHas('product', function($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })->where('quantity', '<=', 10)->count();
        $pendingMessages = ChatMessage::where('receiver_id', $supplierId)
            ->where('read', false)
            ->count();

        // Get recent orders
        $recentOrders = Order::with(['customer', 'items.product'])
            ->whereHas('items.product', function($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function($order) {
                $order->customer_name = optional($order->customer)->name;
                $order->products_count = $order->items->count();
                $order->status_color = $this->getStatusColor($order->status);
                return $order;
            });

        // Get recent messages
        $recentMessages = ChatMessage::with('sender')
            ->where('receiver_id', $supplierId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function($message) {
                $message->sender_name = $message->sender->name;
                return $message;
            });

        // Prepare sales chart data (last 7 days)
        $salesChartData = $this->prepareSalesChartData($supplierId);

        // Prepare inventory chart data
        $inventoryChartData = $this->prepareInventoryChartData($supplierId);

        return view('supplier.dashboard', compact(
            'products',
            'pendingOrders',
            'deliveredOrders',
            'allOrders',
            'factories',
            'totalProducts',
            'pendingOrdersCount',
            'lowStockItems',
            'pendingMessages',
            'recentOrders',
            'recentMessages',
            'salesChartData',
            'inventoryChartData'
        ));
    }

    private function getStatusColor($status)
    {
        return [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ][$status] ?? 'secondary';
    }

    private function prepareSalesChartData($supplierId)
    {
        $dates = collect();
        $sales = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates->push($date->format('M d'));

            $dailySales = Order::whereHas('items.product', function($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->whereDate('created_at', $date)
            ->sum('total_amount');

            $sales->push($dailySales);
        }

        return [
            'labels' => $dates->toArray(),
            'data' => $sales->toArray(),
        ];
    }

    private function prepareInventoryChartData($supplierId)
    {
        $inventory = Inventory::whereHas('product', function($query) use ($supplierId) {
            $query->where('supplier_id', $supplierId);
        })->get();

        $lowStock = $inventory->where('quantity', '<=', 10)->count();
        $mediumStock = $inventory->whereBetween('quantity', [11, 50])->count();
        $highStock = $inventory->where('quantity', '>', 50)->count();

        return [
            'labels' => ['Low Stock', 'Medium Stock', 'High Stock'],
            'data' => [$lowStock, $mediumStock, $highStock],
        ];
    }
}
