<?php

namespace App\Services\MachineLearning;

use Phpml\Regression\LeastSquares;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\ModelManager;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Production;
use App\Models\QualityCheck;

class CoffeeMLService
{
    protected $modelManager;
    protected $modelPath;

    public function __construct()
    {
        $this->modelManager = new ModelManager();
        $this->modelPath = storage_path('app/ml-models');
    }

    /**
     * Train demand prediction model using historical data
     */
    public function trainDemandModel(array $historicalData)
    {
        // Prepare training data
        $samples = [];
        $targets = [];

        foreach ($historicalData as $data) {
            $samples[] = [
                $data['month'],
                $data['season'],
                $data['previous_demand'],
                $data['price'],
                $data['marketing_spend']
            ];
            $targets[] = $data['actual_demand'];
        }

        // Create and train the model
        $regression = new LeastSquares();
        $regression->train($samples, $targets);

        // Save the model
        $this->modelManager->saveToFile($regression, $this->modelPath . '/demand_model.phpml');

        return $regression;
    }

    /**
     * Predict future demand
     */
    public function predictDemand(array $features)
    {
        try {
            $model = $this->modelManager->restoreFromFile($this->modelPath . '/demand_model.phpml');
            return $model->predict($features);
        } catch (\Exception $e) {
            \Log::error('Error predicting demand: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Train quality prediction model
     */
    public function trainQualityModel(array $historicalData)
    {
        $samples = [];
        $targets = [];

        foreach ($historicalData as $data) {
            $samples[] = [
                $data['temperature'],
                $data['humidity'],
                $data['roasting_time'],
                $data['bean_type'],
                $data['processing_method']
            ];
            $targets[] = $data['quality_score'];
        }

        $svc = new SVC(Kernel::RBF);
        $svc->train($samples, $targets);

        $this->modelManager->saveToFile($svc, $this->modelPath . '/quality_model.phpml');

        return $svc;
    }

    /**
     * Predict coffee quality
     */
    public function predictQuality(array $features)
    {
        try {
            $model = $this->modelManager->restoreFromFile($this->modelPath . '/quality_model.phpml');
            return $model->predict($features);
        } catch (\Exception $e) {
            \Log::error('Error predicting quality: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get production optimization recommendations
     */
    public function getProductionOptimization()
    {
        // Get recent production data
        $recentProduction = Production::latest()->take(30)->get();

        // Calculate optimal production parameters
        $recommendations = [
            'optimal_batch_size' => $this->calculateOptimalBatchSize($recentProduction),
            'optimal_roasting_time' => $this->calculateOptimalRoastingTime($recentProduction),
            'suggested_maintenance_schedule' => $this->suggestMaintenanceSchedule($recentProduction)
        ];

        return $recommendations;
    }

    /**
     * Calculate optimal batch size based on historical data
     */
    private function calculateOptimalBatchSize($productionData)
    {
        $efficiencyScores = [];
        foreach ($productionData as $batch) {
            $efficiencyScores[$batch->batch_size] = $this->calculateEfficiencyScore($batch);
        }

        // Return batch size with highest efficiency
        return array_search(max($efficiencyScores), $efficiencyScores);
    }

    /**
     * Calculate efficiency score for a production batch
     */
    private function calculateEfficiencyScore($batch)
    {
        // Consider factors like:
        // - Quality score
        // - Production time
        // - Resource utilization
        // - Waste percentage
        $qualityWeight = 0.4;
        $timeWeight = 0.3;
        $resourceWeight = 0.2;
        $wasteWeight = 0.1;

        $qualityScore = $batch->quality_score ?? 0;
        $timeEfficiency = $this->calculateTimeEfficiency($batch);
        $resourceEfficiency = $this->calculateResourceEfficiency($batch);
        $wasteScore = 100 - ($batch->waste_percentage ?? 0);

        return ($qualityScore * $qualityWeight) +
               ($timeEfficiency * $timeWeight) +
               ($resourceEfficiency * $resourceWeight) +
               ($wasteScore * $wasteWeight);
    }

    /**
     * Calculate optimal roasting time
     */
    private function calculateOptimalRoastingTime($productionData)
    {
        $qualityScores = [];
        foreach ($productionData as $batch) {
            if (!isset($qualityScores[$batch->roasting_time])) {
                $qualityScores[$batch->roasting_time] = [];
            }
            $qualityScores[$batch->roasting_time][] = $batch->quality_score ?? 0;
        }

        // Calculate average quality score for each roasting time
        $averageScores = array_map(function($scores) {
            return array_sum($scores) / count($scores);
        }, $qualityScores);

        // Return roasting time with highest average quality score
        return array_search(max($averageScores), $averageScores);
    }

    /**
     * Suggest maintenance schedule based on production data
     */
    private function suggestMaintenanceSchedule($productionData)
    {
        $performanceDecline = $this->analyzePerformanceDecline($productionData);
        $maintenanceIntervals = $this->calculateMaintenanceIntervals($performanceDecline);

        return [
            'next_maintenance_due' => $this->calculateNextMaintenanceDue($maintenanceIntervals),
            'suggested_interval_days' => $maintenanceIntervals,
            'priority_equipment' => $this->identifyPriorityEquipment($productionData)
        ];
    }

    /**
     * Calculate time efficiency of a production batch
     */
    private function calculateTimeEfficiency($batch)
    {
        $actualTime = $batch->production_time;
        $expectedTime = $batch->expected_production_time;

        if (!$expectedTime) return 0;

        $efficiency = ($expectedTime / $actualTime) * 100;
        return min($efficiency, 100); // Cap at 100%
    }

    /**
     * Calculate resource efficiency of a production batch
     */
    private function calculateResourceEfficiency($batch)
    {
        $actualUsage = $batch->resource_usage;
        $expectedUsage = $batch->expected_resource_usage;

        if (!$expectedUsage) return 0;

        $efficiency = ($expectedUsage / $actualUsage) * 100;
        return min($efficiency, 100); // Cap at 100%
    }

    /**
     * Analyze performance decline patterns
     */
    private function analyzePerformanceDecline($productionData)
    {
        $performanceScores = [];
        foreach ($productionData as $batch) {
            $date = Carbon::parse($batch->created_at)->format('Y-m-d');
            if (!isset($performanceScores[$date])) {
                $performanceScores[$date] = [];
            }
            $performanceScores[$date][] = $this->calculateEfficiencyScore($batch);
        }

        // Calculate daily averages
        return array_map(function($scores) {
            return array_sum($scores) / count($scores);
        }, $performanceScores);
    }

    /**
     * Calculate optimal maintenance intervals
     */
    private function calculateMaintenanceIntervals($performanceDecline)
    {
        // Find pattern of significant performance drops
        $significantDrops = [];
        $previousScore = reset($performanceDecline);
        $day = 0;

        foreach ($performanceDecline as $score) {
            if (($previousScore - $score) > 5) { // 5% drop threshold
                $significantDrops[] = $day;
            }
            $previousScore = $score;
            $day++;
        }

        // Calculate average interval between drops
        if (count($significantDrops) < 2) {
            return 30; // Default to 30 days if not enough data
        }

        $intervals = [];
        for ($i = 1; $i < count($significantDrops); $i++) {
            $intervals[] = $significantDrops[$i] - $significantDrops[$i-1];
        }

        return round(array_sum($intervals) / count($intervals));
    }

    /**
     * Calculate next maintenance due date
     */
    private function calculateNextMaintenanceDue($interval)
    {
        $lastMaintenance = \App\Models\MaintenanceTask::where('status', 'completed')
            ->latest()
            ->first();

        if (!$lastMaintenance) {
            return Carbon::now()->addDays($interval);
        }

        return Carbon::parse($lastMaintenance->completed_at)->addDays($interval);
    }

    /**
     * Identify priority equipment for maintenance
     */
    private function identifyPriorityEquipment($productionData)
    {
        $equipmentIssues = [];

        foreach ($productionData as $batch) {
            if ($batch->equipment_issues) {
                $issues = json_decode($batch->equipment_issues, true);
                foreach ($issues as $equipment => $issue) {
                    if (!isset($equipmentIssues[$equipment])) {
                        $equipmentIssues[$equipment] = 0;
                    }
                    $equipmentIssues[$equipment]++;
                }
            }
        }

        arsort($equipmentIssues);
        return array_slice($equipmentIssues, 0, 3, true);
    }
}
