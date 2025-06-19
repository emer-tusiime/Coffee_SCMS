<?php

namespace App\Http\Controllers\MachineLearning;

use App\Http\Controllers\Controller;
use App\Services\MachineLearning\CoffeeMLService;
use Illuminate\Http\Request;
use App\Models\Production;
use App\Models\Order;
use Carbon\Carbon;

class PredictionController extends Controller
{
    protected $mlService;

    public function __construct(CoffeeMLService $mlService)
    {
        $this->mlService = $mlService;
    }

    /**
     * Show ML insights dashboard
     */
    public function dashboard()
    {
        $predictions = $this->getDemandPredictions();
        $qualityPredictions = $this->getQualityPredictions();
        $optimization = $this->mlService->getProductionOptimization();

        return view('ml.dashboard', compact('predictions', 'qualityPredictions', 'optimization'));
    }

    /**
     * Get demand predictions for next 3 months
     */
    private function getDemandPredictions()
    {
        $predictions = [];
        $currentMonth = Carbon::now();

        for ($i = 1; $i <= 3; $i++) {
            $month = $currentMonth->copy()->addMonths($i);

            $features = [
                $month->month,
                $this->getSeason($month),
                $this->getPreviousMonthDemand(),
                $this->getAveragePrice(),
                $this->getMarketingSpend()
            ];

            $prediction = $this->mlService->predictDemand($features);

            $predictions[] = [
                'month' => $month->format('F Y'),
                'predicted_demand' => round($prediction),
                'confidence' => $this->calculateConfidence($prediction)
            ];
        }

        return $predictions;
    }

    /**
     * Get quality predictions for current production batches
     */
    private function getQualityPredictions()
    {
        $predictions = [];
        $currentBatches = Production::where('status', 'in_progress')->get();

        foreach ($currentBatches as $batch) {
            $features = [
                $batch->temperature,
                $batch->humidity,
                $batch->roasting_time,
                $batch->bean_type,
                $batch->processing_method
            ];

            $prediction = $this->mlService->predictQuality($features);

            $predictions[] = [
                'batch_id' => $batch->id,
                'predicted_quality' => $prediction,
                'factors' => $this->getQualityFactors($features)
            ];
        }

        return $predictions;
    }

    /**
     * Get season based on month
     */
    private function getSeason(Carbon $date)
    {
        $month = $date->month;

        if ($month >= 3 && $month <= 5) return 1; // Spring
        if ($month >= 6 && $month <= 8) return 2; // Summer
        if ($month >= 9 && $month <= 11) return 3; // Fall
        return 4; // Winter
    }

    /**
     * Get previous month's demand
     */
    private function getPreviousMonthDemand()
    {
        $lastMonth = Carbon::now()->subMonth();
        return Order::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('quantity');
    }

    /**
     * Get average price of products
     */
    private function getAveragePrice()
    {
        return Order::whereMonth('created_at', Carbon::now()->month)
            ->avg('unit_price') ?? 0;
    }

    /**
     * Get current marketing spend
     */
    private function getMarketingSpend()
    {
        // This would typically come from a marketing budget table
        // For now, return a default value
        return 10000;
    }

    /**
     * Calculate confidence score for prediction
     */
    private function calculateConfidence($prediction)
    {
        // This would typically be based on model metrics
        // For now, return a random confidence between 85-95%
        return rand(85, 95);
    }

    /**
     * Analyze quality factors
     */
    private function getQualityFactors($features)
    {
        return [
            'temperature' => $this->analyzeTemperature($features[0]),
            'humidity' => $this->analyzeHumidity($features[1]),
            'roasting_time' => $this->analyzeRoastingTime($features[2]),
            'bean_type' => $this->analyzeBeanType($features[3]),
            'processing' => $this->analyzeProcessing($features[4])
        ];
    }

    /**
     * Analyze temperature impact on quality
     */
    private function analyzeTemperature($temp)
    {
        if ($temp < 175) return ['status' => 'low', 'message' => 'Temperature too low for optimal roasting'];
        if ($temp > 230) return ['status' => 'high', 'message' => 'Temperature too high, risk of burning'];
        return ['status' => 'optimal', 'message' => 'Temperature in optimal range'];
    }

    /**
     * Analyze humidity impact on quality
     */
    private function analyzeHumidity($humidity)
    {
        if ($humidity < 30) return ['status' => 'low', 'message' => 'Low humidity may affect bean moisture'];
        if ($humidity > 70) return ['status' => 'high', 'message' => 'High humidity may extend drying time'];
        return ['status' => 'optimal', 'message' => 'Humidity in optimal range'];
    }

    /**
     * Analyze roasting time impact
     */
    private function analyzeRoastingTime($time)
    {
        if ($time < 10) return ['status' => 'low', 'message' => 'Roasting time too short'];
        if ($time > 15) return ['status' => 'high', 'message' => 'Extended roasting time may affect flavor'];
        return ['status' => 'optimal', 'message' => 'Roasting time in optimal range'];
    }

    /**
     * Analyze bean type impact
     */
    private function analyzeBeanType($type)
    {
        $recommendations = [
            'arabica' => 'Optimal for premium products',
            'robusta' => 'Better for espresso blends',
            'mixed' => 'Good for balanced flavor profiles'
        ];

        return [
            'status' => 'info',
            'message' => $recommendations[$type] ?? 'Unknown bean type'
        ];
    }

    /**
     * Analyze processing method impact
     */
    private function analyzeProcessing($method)
    {
        $recommendations = [
            'washed' => 'Clean, bright flavor profile',
            'natural' => 'Fuller body, more complex flavors',
            'honey' => 'Balanced sweetness and acidity'
        ];

        return [
            'status' => 'info',
            'message' => $recommendations[$method] ?? 'Unknown processing method'
        ];
    }
}
