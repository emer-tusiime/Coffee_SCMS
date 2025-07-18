<?php

namespace App\Services\Analytics;

use App\Models\Supplier;
use App\Models\SupplyDelivery;
use App\Models\QualityCheck;
use Carbon\Carbon;

class SupplierAnalyticsService
{
    public function getPerformanceMetrics($supplierId)
    {
        $deliveryRate = $this->calculateDeliveryRate($supplierId);
        $qualityScore = $this->calculateQualityScore($supplierId);

        return [
            'delivery_rate' => $deliveryRate,
            'quality_score' => $qualityScore,
            'status' => $this->determineStatus($deliveryRate, $qualityScore)
        ];
    }

    private function calculateDeliveryRate($supplierId)
    {
        $deliveries = SupplyDelivery::where('supplier_id', $supplierId)
            ->where('expected_date', '>=', Carbon::now()->subDays(30))
            ->get();

        if ($deliveries->isEmpty()) {
            return 0;
        }

        $onTimeDeliveries = $deliveries->filter(function ($delivery) {
            return $delivery->delivery_date <= $delivery->expected_date;
        })->count();

        return round(($onTimeDeliveries / $deliveries->count()) * 100, 2);
    }

    private function calculateQualityScore($supplierId)
    {
        $qualityChecks = QualityCheck::where('supplier_id', $supplierId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->get();

        if ($qualityChecks->isEmpty()) {
            return 0;
        }

        $totalScore = $qualityChecks->sum('score');
        $maxPossibleScore = $qualityChecks->count() * 100;

        return round(($totalScore / $maxPossibleScore) * 100, 2);
    }

    private function determineStatus($deliveryRate, $qualityScore)
    {
        $averageScore = ($deliveryRate + $qualityScore) / 2;

        if ($averageScore >= 90) {
            return 'excellent';
        } elseif ($averageScore >= 80) {
            return 'good';
        } elseif ($averageScore >= 70) {
            return 'fair';
        } else {
            return 'poor';
        }
    }

    public function getTopSuppliers($limit = 5)
    {
        return Supplier::all()->map(function ($supplier) {
            $metrics = $this->getPerformanceMetrics($supplier->id);
            $supplier->performance_score = ($metrics['delivery_rate'] + $metrics['quality_score']) / 2;
            return $supplier;
        })->sortByDesc('performance_score')
          ->take($limit);
    }

    public function getSupplierTrends($supplierId)
    {
        $dates = collect();
        $deliveryRates = collect();
        $qualityScores = collect();

        // Get trends for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $dates->push($date->format('M Y'));

            // Calculate metrics for each month
            $deliveryRates->push($this->calculateMonthlyDeliveryRate($supplierId, $date));
            $qualityScores->push($this->calculateMonthlyQualityScore($supplierId, $date));
        }

        return [
            'labels' => $dates->toArray(),
            'delivery_rates' => $deliveryRates->toArray(),
            'quality_scores' => $qualityScores->toArray(),
        ];
    }

    private function calculateMonthlyDeliveryRate($supplierId, Carbon $date)
    {
        $deliveries = SupplyDelivery::where('supplier_id', $supplierId)
            ->whereYear('expected_date', $date->year)
            ->whereMonth('expected_date', $date->month)
            ->get();

        if ($deliveries->isEmpty()) {
            return 0;
        }

        $onTimeDeliveries = $deliveries->filter(function ($delivery) {
            return $delivery->delivery_date <= $delivery->expected_date;
        })->count();

        return round(($onTimeDeliveries / $deliveries->count()) * 100, 2);
    }

    private function calculateMonthlyQualityScore($supplierId, Carbon $date)
    {
        $qualityChecks = QualityCheck::where('supplier_id', $supplierId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->get();

        if ($qualityChecks->isEmpty()) {
            return 0;
        }

        $totalScore = $qualityChecks->sum('score');
        $maxPossibleScore = $qualityChecks->count() * 100;

        return round(($totalScore / $maxPossibleScore) * 100, 2);
    }

    /**
     * Get performance metrics for all suppliers
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPerformanceMetrics()
    {
        return \App\Models\Supplier::all()->map(function ($supplier) {
            $metrics = $this->getPerformanceMetrics($supplier->id);
            $supplier->delivery_rate = $metrics['delivery_rate'];
            $supplier->quality_score = $metrics['quality_score'];
            $supplier->status = $metrics['status'];
            return $supplier;
        });
    }
}
