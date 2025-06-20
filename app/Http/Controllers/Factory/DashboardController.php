<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\ProductionLine;
use App\Models\QualityIssue;
use App\Models\MaintenanceTask;
use App\Services\MachineLearning\ProductionAnalyticsService;
use App\Services\MachineLearning\QualityPredictionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $productionAnalyticsService;
    protected $qualityPredictionService;

    public function __construct(
        ProductionAnalyticsService $productionAnalyticsService,
        QualityPredictionService $qualityPredictionService
    ) {
        $this->middleware(['auth', 'role:factory']);
        $this->productionAnalyticsService = $productionAnalyticsService;
        $this->qualityPredictionService = $qualityPredictionService;
    }

    public function index()
    {
        // Production Line Statistics
        $productionLines = ProductionLine::withCount(['qualityIssues' => function($query) {
            $query->where('status', 'open');
        }])->get();

        // Today's Quality Issues
        $todayQualityIssues = QualityIssue::whereDate('created_at', Carbon::today())
            ->with('productionLine')
            ->get();

        // Pending Maintenance Tasks
        $pendingMaintenance = MaintenanceTask::where('status', 'pending')
            ->with('productionLine')
            ->get();

        // Production Efficiency
        $productionEfficiency = $this->productionAnalyticsService->getCurrentEfficiency();

        // Quality Predictions
        $qualityPredictions = $this->qualityPredictionService->getQualityPredictions();

        // Workforce Status
        $workforceStatus = $this->getWorkforceStatus();

        return view('factory.dashboard', compact(
            'productionLines',
            'todayQualityIssues',
            'pendingMaintenance',
            'productionEfficiency',
            'qualityPredictions',
            'workforceStatus'
        ));
    }

    public function productionLines()
    {
        $productionLines = ProductionLine::with(['qualityIssues', 'maintenanceTasks'])->get();
        return view('factory.production-lines', compact('productionLines'));
    }

    public function qualityControl()
    {
        $qualityIssues = QualityIssue::with('productionLine')->latest()->paginate(10);
        return view('factory.quality-control', compact('qualityIssues'));
    }

    public function maintenance()
    {
        $maintenanceTasks = MaintenanceTask::with('productionLine')->orderBy('due_date')->paginate(10);
        return view('factory.maintenance', compact('maintenanceTasks'));
    }

    public function workforce()
    {
        $workforceData = $this->getWorkforceStatus();
        return view('factory.workforce', compact('workforceData'));
    }

    private function getWorkforceStatus()
    {
        // Placeholder for workforce management logic
        return [
            'total_workers' => 100,
            'present_today' => 95,
            'on_leave' => 5,
            'shift_distribution' => [
                'morning' => 40,
                'afternoon' => 35,
                'night' => 20
            ]
        ];
    }
}
