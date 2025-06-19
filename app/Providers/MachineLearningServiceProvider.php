<?php

namespace App\Providers;

use App\Services\MachineLearning\ProductionAnalyticsService;
use App\Services\MachineLearning\QualityPredictionService;
use Illuminate\Support\ServiceProvider;

class MachineLearningServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ProductionAnalyticsService::class, function ($app) {
            return new ProductionAnalyticsService();
        });

        $this->app->singleton(QualityPredictionService::class, function ($app) {
            return new QualityPredictionService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
