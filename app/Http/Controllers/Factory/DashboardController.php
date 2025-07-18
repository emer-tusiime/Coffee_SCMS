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
use App\Models\Product;

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
        $factory = auth()->user()->factory;
        $totalProducts = $factory ? Product::where('factory_id', $factory->id)
            ->where('type', 'processed')
            ->count() : 0;
        $activeProducts = $factory ? Product::where('factory_id', $factory->id)->where('status', true)->count() : 0;
        $supplierCount = $factory ? \App\Models\User::where('role', 'supplier')->where('approved', true)->count() : 0;
        $pendingOrders = $factory ? \App\Models\Order::where('factory_id', $factory->id)->where('status', false)->count() : 0;
        $totalOrders = $factory ? \App\Models\Order::where('factory_id', $factory->id)->count() : 0;
        $recentOrders = $factory ? \App\Models\Order::where('factory_id', $factory->id)
            ->with('supplier')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get() : collect();
        // Calculate total products in stock (count of products for this factory)
        $totalProductsInStock = $factory ? Product::where('factory_id', $factory->id)
            ->where('type', 'processed')
            ->count() : 0;
        // Calculate pending supplier orders (order_type = 'supplier', status = 'pending')
        $pendingSupplierOrders = $factory ? \App\Models\Order::where('factory_id', $factory->id)
            ->where('order_type', 'supplier')
            ->where('status', 'pending')
            ->count() : 0;
        // Calculate pending wholesaler orders (order_type = 'wholesaler', status = 'pending')
        $pendingWholesalerOrders = $factory ? \App\Models\Order::where('factory_id', $factory->id)
            ->where('order_type', 'wholesaler')
            ->where('status', 'pending')
            ->count() : 0;
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

        // Add $stats array for dashboard cards
        $stats = [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'pending_orders' => $pendingOrders,
            'total_orders' => $totalOrders,
            'supplier_count' => $supplierCount,
            'recent_orders' => $recentOrders,
            'total_products_in_stock' => $totalProductsInStock,
            'pending_supplier_orders' => $pendingSupplierOrders,
            'pending_wholesaler_orders' => $pendingWholesalerOrders,
        ];

        return view('factory.dashboard', compact(
            'productionLines',
            'todayQualityIssues',
            'pendingMaintenance',
            'productionEfficiency',
            'qualityPredictions',
            'workforceStatus',
            'stats'
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
