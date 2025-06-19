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

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    public function index()
    {
        $supplier = Auth::user();

        // Get counts for overview cards
        $totalProducts = Product::where('supplier_id', $supplier->id)->count();
        $activeOrders = Order::whereHas('items.product', function($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
        })->whereIn('status', ['pending', 'processing'])->count();
        $lowStockItems = Inventory::whereHas('product', function($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
        })->where('quantity', '<=', 10)->count();
        $pendingMessages = ChatMessage::where('receiver_id', $supplier->id)
            ->where('read', false)
            ->count();

        // Get recent orders
        $recentOrders = Order::with(['customer', 'items.product'])
            ->whereHas('items.product', function($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function($order) {
                $order->customer_name = $order->customer->name;
                $order->products_count = $order->items->count();
                $order->status_color = $this->getStatusColor($order->status);
                return $order;
            });

        // Get recent messages
        $recentMessages = ChatMessage::with('sender')
            ->where('receiver_id', $supplier->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function($message) {
                $message->sender_name = $message->sender->name;
                return $message;
            });

        // Prepare sales chart data (last 7 days)
        $salesChartData = $this->prepareSalesChartData($supplier);

        // Prepare inventory chart data
        $inventoryChartData = $this->prepareInventoryChartData($supplier);

        return view('supplier.dashboard', compact(
            'totalProducts',
            'activeOrders',
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

    private function prepareSalesChartData($supplier)
    {
        $dates = collect();
        $sales = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates->push($date->format('M d'));

            $dailySales = Order::whereHas('items.product', function($query) use ($supplier) {
                $query->where('supplier_id', $supplier->id);
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

    private function prepareInventoryChartData($supplier)
    {
        $inventory = Inventory::whereHas('product', function($query) use ($supplier) {
            $query->where('supplier_id', $supplier->id);
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
