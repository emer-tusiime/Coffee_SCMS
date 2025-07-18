<?php

namespace App\Services\MachineLearning;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class MachineLearningService
{
    public function predictTopSellingProducts($limit = 5)
    {
        // Get top selling products based on recent sales
        return Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', true)
            ->where('orders.created_at', '>=', now()->subMonths(3))
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take($limit)
            ->get();
    }

    public function predictSupplierPerformance($limit = 5)
    {
        // Get top suppliers based on recent deliveries
        return Supplier::select('suppliers.*', 
            DB::raw('COUNT(orders.id) as total_orders'),
            DB::raw('AVG(orders.delivery_days) as avg_delivery_days'),
            DB::raw('AVG(quality_issues.quality_score) as avg_quality_score'))
            ->leftJoin('orders', 'suppliers.id', '=', 'orders.supplier_id')
            ->leftJoin('quality_issues', 'orders.id', '=', 'quality_issues.order_id')
            ->where('orders.status', true)
            ->where('orders.created_at', '>=', now()->subMonths(3))
            ->groupBy('suppliers.id')
            ->orderByDesc('total_orders')
            ->take($limit)
            ->get();
    }

    public function predictDemand($productId, $weeks = 4)
    {
        // Predict demand based on historical data
        $historicalSales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $productId)
            ->where('orders.status', true)
            ->where('orders.created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(order_items.quantity) as quantity')
            )
            ->groupBy('date')
            ->get()
            ->groupBy(function($item) {
                return $item->date->format('W'); // Group by week
            });

        // Calculate average weekly sales
        $totalQuantity = 0;
        $weeksCount = 0;
        foreach ($historicalSales as $week => $sales) {
            $totalQuantity += $sales->sum('quantity');
            $weeksCount++;
        }

        $averageWeeklySales = $weeksCount > 0 ? $totalQuantity / $weeksCount : 0;
        
        return [
            'predicted_demand' => $averageWeeklySales * $weeks,
            'confidence_score' => 0.85, // Confidence score between 0 and 1
            'trend' => $this->calculateTrend($historicalSales)
        ];
    }

    private function calculateTrend($historicalSales)
    {
        // Simple trend calculation based on recent weeks
        $weeks = array_keys($historicalSales->toArray());
        $quantities = array_map(function($week) {
            return $historicalSales[$week]->sum('quantity');
        }, $weeks);

        // Calculate slope using linear regression
        $n = count($quantities);
        $sumX = array_sum(range(0, $n-1));
        $sumY = array_sum($quantities);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $i * $quantities[$i];
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);

        return $slope > 0 ? 'up' : ($slope < 0 ? 'down' : 'stable');
    }
}
