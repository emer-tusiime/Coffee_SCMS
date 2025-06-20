<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Supplier\DashboardController as SupplierDashboardController;
use App\Http\Controllers\Supplier\ProductController as SupplierProductController;
use App\Http\Controllers\Supplier\ChatController as SupplierChatController;
use App\Http\Controllers\Supplier\ApplicationController as SupplierApplicationController;
use App\Http\Controllers\Supplier\ReportController as SupplierReportController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Factory\DashboardController as FactoryDashboardController;
use App\Http\Controllers\Retailer\DashboardController as RetailerDashboardController;
use App\Http\Controllers\Wholesaler\DashboardController as WholesalerDashboardController;
use App\Http\Controllers\WorkforceManager\DashboardController as WorkforceManagerDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\MachineLearning\PredictionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ProductionLineController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Root route
Route::get('/', function () {
    // Clear any existing session
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
});

// Guest routes (unauthenticated users only)
Route::middleware('guest')->group(function () {
    // Authentication Routes
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    // Registration Routes
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // Password Reset Routes
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Home route
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Factory routes
    Route::prefix('factory')->name('factory.')->middleware(['auth', 'role:factory'])->group(function () {
        Route::get('/dashboard', [FactoryDashboardController::class, 'index'])->name('dashboard');
        Route::get('/production/lines', [FactoryDashboardController::class, 'productionLines'])->name('production.lines');
        Route::get('/quality/control', [FactoryDashboardController::class, 'qualityControl'])->name('quality.control');
        Route::get('/maintenance', [FactoryDashboardController::class, 'maintenance'])->name('maintenance');
        Route::get('/workforce', [FactoryDashboardController::class, 'workforce'])->name('workforce');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Settings routes
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
        Route::patch('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
        Route::patch('/settings/email', [AdminDashboardController::class, 'updateEmailSettings'])->name('settings.email');

        Route::resource('users', AdminUserController::class);
        // Route::resource('products', AdminProductController::class);
        Route::resource('suppliers', AdminSupplierController::class);

        // Production Lines Management
        Route::resource('production/lines', \App\Http\Controllers\Admin\ProductionLineController::class)->names([
            'index' => 'admin.production.lines.index',
            'create' => 'admin.production.lines.create',
            'store' => 'admin.production.lines.store',
            'show' => 'admin.production.lines.show',
            'edit' => 'admin.production.lines.edit',
            'update' => 'admin.production.lines.update',
            'destroy' => 'admin.production.lines.destroy'
        ]);

        // Orders Management
        Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');

        // Production Management
        Route::get('/production/efficiency', [App\Http\Controllers\Admin\ProductionController::class, 'efficiency'])->name('production.efficiency');
        Route::patch('/production/lines/{productionLine}/efficiency', [App\Http\Controllers\Admin\ProductionController::class, 'updateEfficiency'])->name('production.update-efficiency');

        // Inventory Alerts
        Route::get('/inventory/alerts', [App\Http\Controllers\Admin\InventoryAlertController::class, 'index'])->name('inventory.alerts');

        // Quality Issues
        Route::get('/quality/issues', [App\Http\Controllers\Admin\QualityIssueController::class, 'index'])->name('quality.issues');

        Route::get('/production/schedule', [AdminDashboardController::class, 'productionSchedule'])->name('production.schedule');
        Route::get('/quality/control', [AdminDashboardController::class, 'qualityControl'])->name('quality.control');
        Route::get('/inventory/raw-materials', [AdminDashboardController::class, 'rawMaterials'])->name('inventory.raw-materials');
        Route::get('/inventory/finished-goods', [AdminDashboardController::class, 'finishedGoods'])->name('inventory.finished-goods');
        Route::get('/inventory/tracking', [AdminDashboardController::class, 'inventoryTracking'])->name('inventory.tracking');
        Route::get('/suppliers', [AdminDashboardController::class, 'suppliers'])->name('suppliers.index');
        Route::get('/logistics', [AdminDashboardController::class, 'logistics'])->name('logistics');
        Route::get('/analytics/sales', [AdminDashboardController::class, 'salesAnalytics'])->name('analytics.sales');
        Route::get('/analytics/production', [AdminDashboardController::class, 'productionAnalytics'])->name('analytics.production');
        Route::get('/analytics/quality', [AdminDashboardController::class, 'qualityMetrics'])->name('analytics.quality');
        Route::get('/reports/generate', [AdminDashboardController::class, 'generateReports'])->name('reports.generate');
        Route::get('/ml-insights', [AdminDashboardController::class, 'mlInsights'])->name('ml-insights');
    });

    // Factory routes
    Route::middleware(['auth', 'role:factory'])->prefix('factory')->name('factory.')->group(function () {
        Route::get('/dashboard', [FactoryDashboardController::class, 'index'])->name('dashboard');
        Route::get('production/lines', [\App\Http\Controllers\Factory\ProductionLineController::class, 'index'])->name('production.lines');
        Route::get('quality-control', [\App\Http\Controllers\Factory\QualityControlController::class, 'index'])->name('quality.control');
        Route::get('maintenance', [\App\Http\Controllers\Factory\MaintenanceController::class, 'index'])->name('maintenance');
        Route::get('workforce', [\App\Http\Controllers\Factory\WorkforceController::class, 'index'])->name('workforce');
    });

    // Factory Manager routes
    Route::prefix('factory')->middleware('role:factory_manager')->name('factory.')->group(function () {
        Route::get('/dashboard', [FactoryDashboardController::class, 'index'])->name('dashboard');
        
        // Inventory Management
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
        Route::post('/inventory/update', [InventoryController::class, 'updateStock'])->name('inventory.update');
        Route::get('/inventory/status', [InventoryController::class, 'getInventoryStatus'])->name('inventory.status');
        Route::get('/inventory/alerts', [InventoryController::class, 'getLowStockAlerts'])->name('inventory.alerts');
        
        // Workforce Management
        Route::resource('workforce', WorkforceController::class);
        Route::get('/workforce/status', [WorkforceController::class, 'getWorkforceStatus'])->name('workforce.status');
        Route::get('/workforce/availability/{locationId}/{date}', [WorkforceController::class, 'getShiftAvailability'])->name('workforce.availability');
        
        // Products
        Route::resource('products', SupplierProductController::class);
        
        // Chat
        Route::get('/chat', [SupplierChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [SupplierChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/messages', [SupplierChatController::class, 'getMessages'])->name('chat.messages');
    });

    // Supplier routes
    Route::prefix('supplier')->middleware('role:supplier')->name('supplier.')->group(function () {
        Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
        
        // Products
        Route::resource('products', SupplierProductController::class);
        
        // Chat
        Route::get('/chat', [SupplierChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [SupplierChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/messages', [SupplierChatController::class, 'getMessages'])->name('chat.messages');
        
        // Analytics (to be implemented)
        Route::get('/analytics', [SupplierDashboardController::class, 'analytics'])->name('analytics');

        // Products management
        // Route::resource('products', SupplierProductController::class);

        // Inventory management
        // Route::resource('inventory', SupplierInventoryController::class);

        // Orders management
        // Route::resource('orders', SupplierOrderController::class);

        // Chat functionality
        // Route::get('chat', [SupplierChatController::class, 'index'])->name('chat.index');
        // Route::get('chat/{conversation}', [SupplierChatController::class, 'show'])->name('chat.show');
        // Route::post('chat/send', [SupplierChatController::class, 'send'])->name('chat.send');
        // Route::post('chat/mark-read', [SupplierChatController::class, 'markAsRead'])->name('chat.mark-read');

        // Analytics
        // Route::get('analytics', [SupplierAnalyticsController::class, 'index'])->name('analytics');
        // Route::get('analytics/sales', [SupplierAnalyticsController::class, 'sales'])->name('analytics.sales');
        // Route::get('analytics/inventory', [SupplierAnalyticsController::class, 'inventory'])->name('analytics.inventory');
        //Route::get('analytics', [SupplierAnalyticsController::class, 'index'])->name('analytics');
        //Route::get('analytics/sales', [SupplierAnalyticsController::class, 'sales'])->name('analytics.sales');
        //Route::get('analytics/inventory', [SupplierAnalyticsController::class, 'inventory'])->name('analytics.inventory');

        // Vendor application
        Route::get('application/status', [SupplierApplicationController::class, 'status'])->name('application.status');
        Route::post('application/submit', [SupplierApplicationController::class, 'submit'])->name('application.submit');

        // Reports
        Route::get('reports/export', [SupplierReportController::class, 'export'])->name('reports.export');
    });

    // Customer routes
    Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
    });

    // Retailer routes
    Route::prefix('retailer')->middleware(['auth', 'role:retailer'])->name('retailer.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Retailer\DashboardController::class, 'index'])->name('dashboard');

        // Orders Management
        Route::get('/orders', [App\Http\Controllers\Retailer\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Retailer\OrderController::class, 'show'])->name('orders.show');

        // Products Management
        Route::get('/products', [App\Http\Controllers\Retailer\ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [App\Http\Controllers\Retailer\ProductController::class, 'show'])->name('products.show');

        // Chat
        Route::get('/chat', [App\Http\Controllers\Retailer\ChatController::class, 'index'])->name('chat.index');
        Route::post('/chat/send', [App\Http\Controllers\Retailer\ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/messages', [App\Http\Controllers\Retailer\ChatController::class, 'getMessages'])->name('chat.messages');
    });

    // Customer routes
    Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
        
        // Chat
        Route::get('/chat', [App\Http\Controllers\Customer\ChatController::class, 'index'])->name('chat.index');
        Route::post('/chat/send', [App\Http\Controllers\Customer\ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/messages', [App\Http\Controllers\Customer\ChatController::class, 'getMessages'])->name('chat.messages');
    });

    // Workforce Manager routes
    Route::prefix('workforce_manager')->middleware('role:workforce_manager')->name('workforce_manager.')->group(function () {
        Route::get('/dashboard', [WorkforceManagerDashboardController::class, 'index'])->name('dashboard');
        
        // Workforce management
        Route::get('/workforce', [App\Http\Controllers\WorkforceManager\WorkforceController::class, 'index'])->name('workforce.index');
        
        // Attendance management
        Route::get('/attendance', [App\Http\Controllers\WorkforceManager\EmployeeAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/mark', [App\Http\Controllers\WorkforceManager\EmployeeAttendanceController::class, 'markAttendance'])->name('attendance.mark');
        Route::get('/attendance/report', [App\Http\Controllers\WorkforceManager\EmployeeAttendanceController::class, 'getAttendanceReport'])->name('attendance.report');
        Route::get('/attendance/export', [App\Http\Controllers\WorkforceManager\EmployeeAttendanceController::class, 'exportAttendanceReport'])->name('attendance.export');
    });

    // Wholesaler routes
    Route::prefix('wholesaler')->middleware('role:wholesaler')->name('wholesaler.')->group(function () {
        Route::get('/dashboard', [WholesalerDashboardController::class, 'index'])->name('dashboard');
        // Wholesaler-specific modules
    });

    // Workforce Manager routes
    Route::prefix('workforce_manager')->middleware('role:workforce_manager')->name('workforce_manager.')->group(function () {
        Route::get('/dashboard', [WorkforceManagerDashboardController::class, 'index'])->name('dashboard');
        // Workforce management modules
    });

    // ML Routes
    Route::prefix('ml')->name('ml.')->group(function () {
        Route::get('/dashboard', [PredictionController::class, 'dashboard'])->name('dashboard');
    });
});

// Fallback route for 404
Route::fallback(function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->getDashboardRoute());
    }
    return redirect()->route('login');
});
