<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\QualityIssue;
use App\Models\ProductionLine;
use App\Models\Supplier;
use App\Models\InventoryAlert;
use App\Services\MachineLearning\DemandPredictionService;
use App\Services\MachineLearning\QualityPredictionService;
use App\Services\Analytics\ProductionAnalyticsService;
use App\Services\Analytics\SupplierAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    protected $demandPredictionService;
    protected $qualityPredictionService;
    protected $productionAnalyticsService;
    protected $supplierAnalyticsService;

    public function __construct(
        DemandPredictionService $demandPredictionService,
        QualityPredictionService $qualityPredictionService,
        ProductionAnalyticsService $productionAnalyticsService,
        SupplierAnalyticsService $supplierAnalyticsService
    ) {
        $this->middleware(['auth', 'role:admin']);
        $this->demandPredictionService = $demandPredictionService;
        $this->qualityPredictionService = $qualityPredictionService;
        $this->productionAnalyticsService = $productionAnalyticsService;
        $this->supplierAnalyticsService = $supplierAnalyticsService;
    }

    public function index()
    {
        // Get total orders count
        $totalOrders = Order::count();

        // Calculate production efficiency (example calculation)
        $productionEfficiency = ProductionLine::avg('efficiency') ?? 0;
        $productionEfficiency = round($productionEfficiency);

        // Get inventory alerts count (products with low stock)
        $inventoryAlerts = Product::where('stock', '<', 10)->count();

        // Get quality issues count
        $qualityIssues = QualityIssue::where('status', 'open')->count();

        // Get production line data for chart
        $productionLines = ProductionLine::select('name', 'efficiency', 'status')
            ->get()
            ->map(function ($line) {
                return [
                    'name' => $line->name,
                    'efficiency' => $line->efficiency,
                    'status' => $line->status
                ];
            });

        // ML Insights - Demand Prediction
        $demandPrediction = $this->getDemandPredictionData();

        // Recent Quality Issues
        $recentQualityIssues = QualityIssue::with('productionLine')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($issue) {
                $issue->status_color = $this->getStatusColor($issue->status);
                return $issue;
            });

        // Supplier Performance
        $supplierPerformance = $this->getSupplierPerformanceData();

        // Get user statistics
        $totalUsers = User::count();
        $customerCount = User::where('role', 'customer')->count();
        $supplierCount = User::where('role', 'supplier')->count();

        return view('admin.dashboard', compact(
            'totalOrders',
            'productionEfficiency',
            'inventoryAlerts',
            'qualityIssues',
            'productionLines',
            'demandPrediction',
            'recentQualityIssues',
            'supplierPerformance',
            'totalUsers',
            'customerCount',
            'supplierCount'
        ));
    }

    private function getProductionLineData()
    {
        $productionLines = ProductionLine::all();
        $data = [
            'labels' => [],
            'output' => []
        ];

        foreach ($productionLines as $line) {
            $data['labels'][] = $line->name;
            $data['output'][] = $this->productionAnalyticsService->getLineOutput($line->id);
        }

        return $data;
    }

    private function getDemandPredictionData()
    {
        $dates = collect();
        $actual = collect();
        $predicted = collect();

        // Get data for the next 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i);
            $dates->push($date->format('M d'));

            // Get actual orders for past dates
            if ($i === 0) {
                $actual->push(Order::whereDate('created_at', $date)->count());
            } else {
                $actual->push(null);
            }

            // Get predicted demand
            $predicted->push($this->demandPredictionService->getPrediction($date));
        }

        return [
            'labels' => $dates->toArray(),
            'actual' => $actual->toArray(),
            'predicted' => $predicted->toArray(),
        ];
    }

    private function getSupplierPerformanceData()
    {
        return Supplier::select('id', 'name')
            ->take(5)
            ->get()
            ->map(function ($supplier) {
                $performance = $this->supplierAnalyticsService->getPerformanceMetrics($supplier->id);

                $supplier->delivery_rate = $performance['delivery_rate'];
                $supplier->quality_score = $performance['quality_score'];
                $supplier->status = $performance['status'];
                $supplier->status_color = $this->getSupplierStatusColor($performance['status']);

                return $supplier;
            });
    }

    private function getStatusColor($status)
    {
        return [
            'open' => 'danger',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
        ][$status] ?? 'secondary';
    }

    private function getSupplierStatusColor($status)
    {
        return [
            'excellent' => 'success',
            'good' => 'primary',
            'fair' => 'warning',
            'poor' => 'danger',
        ][$status] ?? 'secondary';
    }

    /**
     * Show the admin settings page.
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Update general settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'timezone' => 'required|string|timezone',
            'date_format' => 'required|string|in:Y-m-d,d/m/Y,m/d/Y'
        ]);

        // Update settings in config
        config(['app.name' => $validated['company_name']]);
        config(['app.timezone' => $validated['timezone']]);

        // Save settings to .env file
        $this->updateEnvFile([
            'APP_NAME' => '"'.$validated['company_name'].'"',
            'APP_TIMEZONE' => $validated['timezone']
        ]);

        return redirect()->route('admin.settings')
            ->with('success', 'General settings updated successfully.');
    }

    /**
     * Update email settings.
     */
    public function updateEmailSettings(Request $request)
    {
        $validated = $request->validate([
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255'
        ]);

        // Update mail settings in config
        config(['mail.from.address' => $validated['mail_from_address']]);
        config(['mail.from.name' => $validated['mail_from_name']]);

        // Save settings to .env file
        $this->updateEnvFile([
            'MAIL_FROM_ADDRESS' => $validated['mail_from_address'],
            'MAIL_FROM_NAME' => '"'.$validated['mail_from_name'].'"'
        ]);

        return redirect()->route('admin.settings')
            ->with('success', 'Email settings updated successfully.');
    }

    /**
     * Update the .env file with new values.
     */
    private function updateEnvFile(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $str .= "\n"; // In case the searched variable is not found
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
            }
        }

        $str = substr($str, 0, -1);
        file_put_contents($envFile, $str);
    }
}
