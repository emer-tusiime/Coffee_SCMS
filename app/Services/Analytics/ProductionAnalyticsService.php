<?php

namespace App\Services\Analytics;

use App\Models\ProductionLine;
use App\Models\ProductionBatch;
use Carbon\Carbon;

class ProductionAnalyticsService
{
    public function getCurrentEfficiency()
    {
        $productionLines = ProductionLine::all();
        $totalEfficiency = 0;
        $count = 0;

        foreach ($productionLines as $line) {
            $efficiency = $this->calculateLineEfficiency($line);
            if ($efficiency !== null) {
                $totalEfficiency += $efficiency;
                $count++;
            }
        }

        return $count > 0 ? round($totalEfficiency / $count, 2) : 0;
    }

    public function getLineOutput($lineId)
    {
        return ProductionBatch::where('production_line_id', $lineId)
            ->whereDate('created_at', Carbon::today())
            ->sum('output_quantity');
    }

    private function calculateLineEfficiency(ProductionLine $line)
    {
        $today = Carbon::today();

        $batches = ProductionBatch::where('production_line_id', $line->id)
            ->whereDate('created_at', $today)
            ->get();

        if ($batches->isEmpty()) {
            return null;
        }

        $actualOutput = $batches->sum('output_quantity');
        $targetOutput = $batches->sum('target_quantity');

        if ($targetOutput === 0) {
            return 0;
        }

        return round(($actualOutput / $targetOutput) * 100, 2);
    }

    public function getQualityMetrics($lineId)
    {
        $batches = ProductionBatch::where('production_line_id', $lineId)
            ->whereDate('created_at', Carbon::today())
            ->get();

        $totalProducts = $batches->sum('output_quantity');
        $defectiveProducts = $batches->sum('defective_quantity');

        if ($totalProducts === 0) {
            return 0;
        }

        return round((($totalProducts - $defectiveProducts) / $totalProducts) * 100, 2);
    }

    public function getDowntime($lineId)
    {
        // Calculate total downtime in minutes for today
        return ProductionLine::find($lineId)
            ->downtimes()
            ->whereDate('start_time', Carbon::today())
            ->sum('duration_minutes');
    }
}
