<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Period;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MachineLearningService
{
    public function predictTopSellingProducts(int $months = 3): Collection
    {
        $startDate = now()->subMonths($months);
        $orders = Order::where('created_at', '>=', $startDate)
            ->where('status', true)
            ->with(['items.product'])
            ->get();

        $productSales = collect();
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $productSales->push([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                    'created_at' => $order->created_at
                ]);
            }
        }

        return $productSales
            ->groupBy('product_id')
            ->map(function ($items) {
                return [
                    'total_quantity' => $items->sum('quantity'),
                    'total_value' => $items->sum('total'),
                    'average_order_value' => $items->avg('total'),
                    'sales_frequency' => count($items)
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(10);
    }

    public function predictSupplierPerformance(int $months = 3): Collection
    {
        $startDate = now()->subMonths($months);
        $supplierOrders = Order::where('created_at', '>=', $startDate)
            ->where('order_type', 'supplier')
            ->where('status', true)
            ->with(['supplier'])
            ->get();

        return $supplierOrders
            ->groupBy('supplier_id')
            ->map(function ($orders, $supplierId) {
                $supplier = User::find($supplierId);
                return [
                    'supplier_id' => $supplierId,
                    'supplier_name' => $supplier ? $supplier->name : 'Unknown',
                    'total_orders' => count($orders),
                    'total_value' => $orders->sum('total'),
                    'average_delivery_time' => $this->calculateAverageDeliveryTime($orders),
                    'on_time_delivery_rate' => $this->calculateOnTimeDeliveryRate($orders),
                    'quality_score' => $this->calculateQualityScore($orders)
                ];
            })
            ->sortByDesc('total_value')
            ->take(10);
    }

    private function calculateAverageDeliveryTime($orders): float
    {
        return $orders
            ->filter(function ($order) {
                return $order->delivered_at && $order->created_at;
            })
            ->avg(function ($order) {
                return $order->delivered_at->diffInDays($order->created_at);
            }) ?? 0;
    }

    private function calculateOnTimeDeliveryRate($orders): float
    {
        $onTime = $orders
            ->filter(function ($order) {
                return $order->delivered_at && 
                       $order->delivered_at <= $order->estimated_delivery_date;
            })
            ->count();

        return ($orders->count() > 0) ? ($onTime / $orders->count()) * 100 : 0;
    }

    private function calculateQualityScore($orders): float
    {
        $qualityIssues = $orders
            ->flatMap(function ($order) {
                return $order->items->flatMap(function ($item) {
                    return $item->product->quality_issues;
                });
            })
            ->count();

        $totalOrders = $orders->count();
        return ($totalOrders > 0) ? (1 - ($qualityIssues / $totalOrders)) * 100 : 100;
    }
}
