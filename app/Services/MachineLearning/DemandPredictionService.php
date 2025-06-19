<?php

namespace App\Services\MachineLearning;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class DemandPredictionService
{
    protected $apiEndpoint;
    protected $apiKey;

    public function __construct()
    {
        $this->apiEndpoint = config('services.ml.endpoint');
        $this->apiKey = config('services.ml.api_key');
    }

    public function getPrediction(Carbon $date)
    {
        // Get historical data for training
        $historicalData = $this->getHistoricalData();

        // In a production environment, this would make an API call to an ML service
        // For now, we'll use a simple algorithm based on historical data
        return $this->calculatePrediction($historicalData, $date);
    }

    private function getHistoricalData()
    {
        return Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
    }

    private function calculatePrediction($historicalData, Carbon $date)
    {
        // Simple moving average calculation
        $average = $historicalData->avg('count');

        // Add some seasonality based on day of week
        $dayOfWeek = $date->dayOfWeek;
        $seasonalFactor = $this->getSeasonalFactor($dayOfWeek);

        return round($average * $seasonalFactor);
    }

    private function getSeasonalFactor($dayOfWeek)
    {
        // Adjust these factors based on your business patterns
        return [
            0 => 0.7,  // Sunday
            1 => 1.2,  // Monday
            2 => 1.1,  // Tuesday
            3 => 1.0,  // Wednesday
            4 => 1.1,  // Thursday
            5 => 1.3,  // Friday
            6 => 0.8,  // Saturday
        ][$dayOfWeek];
    }
}
