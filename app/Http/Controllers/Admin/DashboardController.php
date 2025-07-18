<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\QualityIssue;
use App\Models\ProductionLine;
use App\Models\Supplier;
use App\Models\InventoryAlert;
use App\Models\CoffeeSale;
use App\Services\MachineLearning\MachineLearningService;
use App\Services\MachineLearning\DemandPredictionService;
use App\Services\MachineLearning\QualityPredictionService;
use App\Services\Analytics\ProductionAnalyticsService;
use App\Services\Analytics\SupplierAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $machineLearningService;
    protected $demandPredictionService;
    protected $qualityPredictionService;
    protected $productionAnalyticsService;
    protected $supplierAnalyticsService;

    public function __construct(
        MachineLearningService $machineLearningService,
        DemandPredictionService $demandPredictionService,
        QualityPredictionService $qualityPredictionService,
        ProductionAnalyticsService $productionAnalyticsService,
        SupplierAnalyticsService $supplierAnalyticsService
    ) {
        $this->middleware(['auth', 'role:admin']);
        $this->machineLearningService = $machineLearningService;
        $this->demandPredictionService = $demandPredictionService;
        $this->qualityPredictionService = $qualityPredictionService;
        $this->productionAnalyticsService = $productionAnalyticsService;
        $this->supplierAnalyticsService = $supplierAnalyticsService;
    }

    public function index()
    {
        $totalOrders = \App\Models\Order::count();
        $productionEfficiency = \App\Models\ProductionLine::avg('efficiency') ?? 0;
        $inventoryAlertsCount = \App\Models\Product::where('stock', '<', 10)->count();
        $totalQualityIssues = \App\Models\QualityIssue::count();
        $totalCoffeeSales = \App\Models\Product::where('type', 'raw')->sum('stock');
        $totalCoffeeValue = \App\Models\Order::sum('total_amount');
        $pendingCoffeeSales = \App\Models\Order::where('status', 'pending')->count();

        $recentInventoryAlerts = \App\Models\Product::where('stock', '<', 10)->orderBy('updated_at', 'desc')->take(5)->get();
        $recentQualityIssues = \App\Models\QualityIssue::orderBy('created_at', 'desc')->take(5)->get();
        $topSellingProducts = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        // Best-practice: Get top 5 suppliers by performance using SupplierAnalyticsService
        $topSuppliers = $this->supplierAnalyticsService->getTopSuppliers(5)->map(function ($supplier) {
            // For chart/table compatibility, add expected fields
            return (object) [
                'supplier_name' => $supplier->name,
                'on_time_delivery_rate' => $supplier->delivery_rate ?? 0,
                'quality_score' => $supplier->quality_score ?? 0,
                'total_value' => $supplier->total_value ?? 0, // Optional: if you want to show value
            ];
        });

        return view('admin.dashboard', compact(
            'totalOrders', 'productionEfficiency', 'inventoryAlertsCount', 'totalQualityIssues',
            'totalCoffeeSales', 'totalCoffeeValue', 'pendingCoffeeSales',
            'recentInventoryAlerts', 'recentQualityIssues', 'topSellingProducts', 'topSuppliers'
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

    /**
     * Show sales analytics
     */
    public function salesAnalytics(Request $request)
    {
        // Top supplier by demand
        $topSupplier = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function($q) { $q->where('order_type', 'supplier'); })
            ->with('product.supplier')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->first();

        // Top factory by demand
        $topFactory = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function($q) { $q->where('order_type', 'wholesaler'); })
            ->with('product.factory')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->first();

        // Top wholesaler by demand
        $topWholesaler = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function($q) { $q->where('order_type', 'retailer'); })
            ->with('order.wholesaler')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->first();

        // Prepare data for charting
        $labels = ['Supplier', 'Factory', 'Wholesaler'];
        $data = [
            $topSupplier ? $topSupplier->total_quantity : 0,
            $topFactory ? $topFactory->total_quantity : 0,
            $topWholesaler ? $topWholesaler->total_quantity : 0,
        ];

        return view('admin.sales-analytics', compact('labels', 'data', 'topSupplier', 'topFactory', 'topWholesaler'));
    }

    private function getDemandPredictionData()
    {
        $dates = collect([]);
        $actual = collect([]);
        $predicted = collect([]);

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
            'dates' => $dates->toArray(),
            'actual' => $actual->toArray(),
            'predicted' => $predicted->toArray()
        ];
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

    public function mlInsights()
    {
        // Top selling products by supplier
        $topSupplierProducts = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function($q) { $q->where('order_type', 'supplier'); })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        // Top selling products by factory
        $topFactoryProducts = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function($q) { $q->where('order_type', 'wholesaler'); })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        // Top selling products by wholesaler
        $topWholesalerProducts = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function($q) { $q->where('order_type', 'retailer'); })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        // Example: Predict future demand for the top product at each level (replace with real ML call)
        $futureSupplierPrediction = $topSupplierProducts->first() ? [
            'dates' => ['2025-07-16','2025-07-17','2025-07-18','2025-07-19','2025-07-20'],
            'predicted' => [120, 130, 140, 150, 160]
        ] : [];
        $futureFactoryPrediction = $topFactoryProducts->first() ? [
            'dates' => ['2025-07-16','2025-07-17','2025-07-18','2025-07-19','2025-07-20'],
            'predicted' => [80, 90, 100, 110, 120]
        ] : [];
        $futureWholesalerPrediction = $topWholesalerProducts->first() ? [
            'dates' => ['2025-07-16','2025-07-17','2025-07-18','2025-07-19','2025-07-20'],
            'predicted' => [60, 70, 80, 90, 100]
        ] : [];

        return view('admin.ml-insights', [
            'topSupplierProducts' => $topSupplierProducts,
            'topFactoryProducts' => $topFactoryProducts,
            'topWholesalerProducts' => $topWholesalerProducts,
            'futureSupplierPrediction' => $futureSupplierPrediction,
            'futureFactoryPrediction' => $futureFactoryPrediction,
            'futureWholesalerPrediction' => $futureWholesalerPrediction,
            'topSupplierProduct' => $topSupplierProducts->first(),
            'topFactoryProduct' => $topFactoryProducts->first(),
            'topWholesalerProduct' => $topWholesalerProducts->first(),
        ]);
    }

    public function rawMaterials(Request $request)
    {
        $suppliers = \App\Models\User::where('role', 'supplier')->get();
        $selectedSupplierId = $request->input('supplier_id');

        $query = \App\Models\Product::where('type', 'raw')->with('supplier');
        if ($selectedSupplierId) {
            $query->where('supplier_id', $selectedSupplierId);
        }
        $rawProducts = $query->get();

        $totalProducts = $rawProducts->count();
        $totalStock = $rawProducts->sum('stock');
        $totalStockValue = $rawProducts->sum(function($product) {
            return $product->stock * $product->price;
        });

        $selectedSupplier = $suppliers->where('id', $selectedSupplierId)->first();

        return view('admin.inventory.raw-materials', compact(
            'rawProducts', 'suppliers', 'selectedSupplierId', 'totalProducts', 'totalStock', 'totalStockValue', 'selectedSupplier'
        ));
    }

    public function finishedGoods()
    {
        $finishedGoods = \App\Models\Product::where('type', 'raw')
            ->where('stock', '<=', 0)
            ->with('supplier')
            ->get();
        return view('admin.inventory.finished-goods', compact('finishedGoods'));
    }
}
