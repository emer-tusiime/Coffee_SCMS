<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Supplier\DashboardController as SupplierDashboardController;
// use App\Http\Controllers\Supplier\ProductController as SupplierProductController;
use App\Http\Controllers\Supplier\ChatController as SupplierChatController;
use App\Http\Controllers\Supplier\ApplicationController as SupplierApplicationController;
// use App\Http\Controllers\Supplier\ReportController as SupplierReportController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Factory\DashboardController as FactoryDashboardController;
use App\Http\Controllers\Retailer\DashboardController as RetailerDashboardController;
use App\Http\Controllers\Wholesaler\DashboardController as WholesalerDashboardController;
// use App\Http\Controllers\WorkforceManager\DashboardController as WorkforceManagerDashboardController;
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
    Route::get('registration-success', function () {
        return view('auth.registration-success');
    })->name('registration.success');

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
        Route::get('/maintenance', [FactoryDashboardController::class, 'maintenance'])->name('maintenance');
        Route::get('/workforce', [FactoryDashboardController::class, 'workforce'])->name('workforce');
        Route::post('complaints', [\App\Http\Controllers\Factory\ComplaintController::class, 'store'])->name('complaints.store');
        Route::resource('products', \App\Http\Controllers\Factory\ProductController::class);
        Route::get('suppliers', [\App\Http\Controllers\Factory\SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/create', [\App\Http\Controllers\Factory\SupplierController::class, 'create'])->name('suppliers.create');
        Route::get('suppliers/{supplier}', [\App\Http\Controllers\Factory\SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('products', [\App\Http\Controllers\Factory\ProductController::class, 'index'])->name('products.index');
        Route::get('orders', [\App\Http\Controllers\Factory\OrderController::class, 'index'])->name('orders.index');
        Route::post('orders', [\App\Http\Controllers\Factory\OrderController::class, 'store'])->name('orders.store');
        Route::post('orders/{order}/approve', [\App\Http\Controllers\Factory\OrderController::class, 'approve'])->name('orders.approve');
        Route::post('orders/{order}/reject', [\App\Http\Controllers\Factory\OrderController::class, 'reject'])->name('orders.reject');
        Route::get('/orders/create', [\App\Http\Controllers\Factory\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [\App\Http\Controllers\Factory\OrderController::class, 'store'])->name('orders.store');
        // AJAX endpoint for fetching products by supplier
        Route::get('/suppliers/{supplier}/products', [\App\Http\Controllers\Factory\OrderController::class, 'getSupplierProducts'])->name('suppliers.products');
        Route::get('/wholesaler/orders', [\App\Http\Controllers\Factory\OrderController::class, 'wholesalerOrders'])->name('wholesaler.orders');
        Route::get('orders/{order}', [\App\Http\Controllers\Factory\OrderController::class, 'show'])->name('orders.show');
        Route::get('inventory', [\App\Http\Controllers\Factory\InventoryController::class, 'index'])->name('inventory.index');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
        Route::patch('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::patch('/settings/email', [App\Http\Controllers\Admin\SettingsController::class, 'updateEmail'])->name('settings.email');
        Route::get('/analytics/sales', [App\Http\Controllers\Admin\DashboardController::class, 'salesAnalytics'])->name('admin.analytics.sales');

        // Coffee Sales Routes
        Route::prefix('coffee-sales')->name('coffee-sales.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\CoffeeSalesController::class, 'index'])->name('index');
            Route::get('/analytics', [App\Http\Controllers\Admin\CoffeeSalesController::class, 'analytics'])->name('analytics');
            Route::post('/{sale}/approve', [App\Http\Controllers\Admin\CoffeeSalesController::class, 'approve'])->name('approve');
            Route::post('/{sale}/reject', [App\Http\Controllers\Admin\CoffeeSalesController::class, 'reject'])->name('reject');
        });

        // User account approval routes
        Route::post('/users/{user}/approve', [\App\Http\Controllers\Admin\UserController::class, 'approve'])->name('accounts.approve');
        Route::post('/users/{user}/reject', [\App\Http\Controllers\Admin\UserController::class, 'reject'])->name('accounts.reject');
        
        // View user dashboard (read-only)
        Route::get('/users/{user}/dashboard', [\App\Http\Controllers\Admin\UserController::class, 'viewDashboard'])->name('users.view-dashboard');

        // Users resource routes
        Route::resource('users', AdminUserController::class);

        // Route::resource('products', AdminProductController::class);
        Route::resource('suppliers', AdminSupplierController::class);
        
        // Production routes
        Route::prefix('production')->name('production.')->group(function () {
            Route::resource('lines', ProductionLineController::class);
            Route::get('/schedule', [ProductionLineController::class, 'schedule'])->name('schedule');
        });


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

        Route::get('/production/schedule', [App\Http\Controllers\Admin\DashboardController::class, 'productionSchedule'])->name('production.schedule');
        Route::get('/quality/control', [App\Http\Controllers\Admin\DashboardController::class, 'qualityControl'])->name('quality.control');
        Route::get('/inventory/raw-materials', [App\Http\Controllers\Admin\DashboardController::class, 'rawMaterials'])->name('inventory.raw-materials');
        Route::get('/inventory/finished-goods', [App\Http\Controllers\Admin\DashboardController::class, 'finishedGoods'])->name('inventory.finished-goods');
        Route::get('/inventory/tracking', [App\Http\Controllers\Admin\DashboardController::class, 'inventoryTracking'])->name('inventory.tracking');
        Route::get('/suppliers', [App\Http\Controllers\Admin\DashboardController::class, 'suppliers'])->name('suppliers.index');
        Route::get('/logistics', [AdminDashboardController::class, 'logistics'])->name('logistics');
        Route::get('/analytics/sales', [AdminDashboardController::class, 'salesAnalytics'])->name('analytics.sales');
        Route::get('/analytics/production', [\App\Http\Controllers\Admin\ProductionController::class, 'efficiency'])->name('analytics.production');
        Route::get('/analytics/quality', [AdminDashboardController::class, 'qualityMetrics'])->name('analytics.quality');
        Route::get('/reports/generate', [AdminDashboardController::class, 'generateReports'])->name('reports.generate');
        Route::get('/ml-insights', [AdminDashboardController::class, 'mlInsights'])->name('ml-insights');
    });

    // Factory Manager routes
    // Route::prefix('factory')->middleware('role:factory_manager')->name('factory.')->group(function () {
    //     Route::get('/dashboard', [FactoryDashboardController::class, 'index'])->name('dashboard');
    //     // ... other routes ...
    // });

    // Supplier routes
    Route::prefix('supplier')->middleware('role:supplier')->name('supplier.')->group(function () {
        Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
        
        // Products
        Route::get('products/create', [\App\Http\Controllers\Supplier\ProductController::class, 'create'])->name('products.create');
        Route::post('products', [\App\Http\Controllers\Supplier\ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [\App\Http\Controllers\Supplier\ProductController::class, 'edit'])->name('products.edit');
        Route::patch('products/{product}/update-stock', [\App\Http\Controllers\Supplier\ProductController::class, 'updateStock'])->name('products.updateStock');
        Route::patch('products/{product}', [\App\Http\Controllers\Supplier\ProductController::class, 'update'])->name('products.update');
        
        // Chat
        Route::get('/chat', [SupplierChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [SupplierChatController::class, 'sendMessage'])->name('chat.send');
        
        // Order Routes
        Route::resource('orders', \App\Http\Controllers\Supplier\OrderController::class);
        Route::patch('orders/{order}/accept', [\App\Http\Controllers\Supplier\OrderController::class, 'accept'])->name('orders.accept');
        Route::patch('orders/{order}/reject', [\App\Http\Controllers\Supplier\OrderController::class, 'reject'])->name('orders.reject');
        Route::post('orders/{order}/update-delivery', [\App\Http\Controllers\Supplier\OrderController::class, 'updateDeliveryStatus'])->name('orders.update-delivery');
        Route::post('orders/{order}/quality-check', [\App\Http\Controllers\Supplier\OrderController::class, 'qualityCheck'])->name('orders.quality-check');
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
        // Route::get('reports/export', [SupplierReportController::class, 'export'])->name('reports.export');
        Route::resource('products', \App\Http\Controllers\Supplier\ProductController::class);
    });

    // Customer routes
    Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        // Route::get('/orders', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
        // Route::get('/orders/{order}', [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
    });

    // Retailer routes
    Route::prefix('retailer')->middleware(['auth', 'role:retailer'])->name('retailer.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Retailer\DashboardController::class, 'index'])->name('dashboard');

        // Orders Management
        Route::get('/orders', [App\Http\Controllers\Retailer\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [App\Http\Controllers\Retailer\OrderController::class, 'create'])->name('orders.create');
        Route::get('/orders/{order}', [App\Http\Controllers\Retailer\OrderController::class, 'show'])->name('orders.show');
        Route::post('orders', [\App\Http\Controllers\Retailer\OrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/rate', [App\Http\Controllers\Retailer\OrderController::class, 'rate'])->name('orders.rate');
        Route::post('/orders/{order}/cancel', [App\Http\Controllers\Retailer\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/make-order', [App\Http\Controllers\Retailer\OrderController::class, 'create'])->name('makeOrder');
        Route::post('/make-order', [App\Http\Controllers\Retailer\OrderController::class, 'store'])->name('makeOrder.store');

        // Products Management
        Route::get('/products', [App\Http\Controllers\Retailer\ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [App\Http\Controllers\Retailer\ProductController::class, 'show'])->name('products.show');

        // Chat
        Route::get('/chat', [App\Http\Controllers\Retailer\ChatController::class, 'index'])->name('chat.index');
        Route::post('/chat/send', [App\Http\Controllers\Retailer\ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/messages', [App\Http\Controllers\Retailer\ChatController::class, 'getMessages'])->name('chat.messages');

        // Complaints
        Route::post('complaints', [\App\Http\Controllers\Retailer\ComplaintController::class, 'store'])->name('complaints.store');
        Route::get('/select-wholesaler', [App\Http\Controllers\Retailer\WholesalerController::class, 'selectWholesaler'])->name('select.wholesaler');
        Route::get('/wholesaler/{wholesaler}/products', [App\Http\Controllers\Retailer\WholesalerController::class, 'wholesalerProducts'])->name('wholesaler.products');
        Route::post('/wholesaler/{wholesaler}/add-to-cart', [App\Http\Controllers\Retailer\WholesalerController::class, 'addToCart'])->name('wholesaler.addToCart');
        Route::get('/wholesaler/{wholesaler}/cart', [App\Http\Controllers\Retailer\WholesalerController::class, 'viewCart'])->name('wholesaler.cart');
        Route::post('/wholesaler/{wholesaler}/place-order', [App\Http\Controllers\Retailer\WholesalerController::class, 'placeOrder'])->name('wholesaler.placeOrder');
    });

    // Customer routes
    Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        // Route::get('/orders', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
        // Route::get('/orders/{order}', [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
        
        // Chat
        Route::get('/chat', [App\Http\Controllers\Customer\ChatController::class, 'index'])->name('chat.index');
        Route::post('/chat/send', [App\Http\Controllers\Customer\ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/chat/messages', [App\Http\Controllers\Customer\ChatController::class, 'getMessages'])->name('chat.messages');
    });

    // Workforce Manager routes
    // Route::prefix('workforce_manager')->middleware('role:workforce_manager')->name('workforce_manager.')->group(function () {
    //     Route::get('/dashboard', [WorkforceManagerDashboardController::class, 'index'])->name('dashboard');
    //     // Workforce management
    //     Route::get('/workforce', [App\Http\Controllers\WorkforceManager\WorkforceController::class, 'index'])->name('workforce.index');
    //     // Attendance management
    //     Route::get('/attendance', [App\Http\Controllers\WorkforceManager\EmployeeAttendanceController::class, 'index'])->name('attendance.index');
    //     Route::post('/attendance/mark', [App\Http\Controllers\WorkforceManager\EmployeeAttendanceController::class, 'markAttendance'])->name('attendance.mark');
    //     Route::get('/attendance/report', [App\Http\Controllers\WorkforceManager\EmployeeAttendanceController::class, 'getAttendanceReport'])->name('attendance.report');
    //     Route::get('/attendance/export', [App\Http\Controllers\WorkforceManager\EmployeeAttendanceController::class, 'exportAttendanceReport'])->name('attendance.export');
    // });

    // Wholesaler routes
    Route::prefix('wholesaler')->middleware('role:wholesaler')->name('wholesaler.')->group(function () {
        Route::get('/dashboard', [WholesalerDashboardController::class, 'index'])->name('dashboard');
        // Wholesaler-specific modules
        Route::get('/orders/create', [\App\Http\Controllers\Wholesaler\OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [\App\Http\Controllers\Wholesaler\OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders', [\App\Http\Controllers\Wholesaler\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Wholesaler\OrderController::class, 'show'])->name('orders.show');
        Route::get('/retailer/orders', [\App\Http\Controllers\Wholesaler\RetailerOrderController::class, 'index'])->name('retailer.orders.index');
        Route::get('/retailer-orders/{order}', [App\Http\Controllers\Wholesaler\RetailerOrderController::class, 'show'])->name('retailer_orders.show');
        Route::get('/reports', [\App\Http\Controllers\Wholesaler\ReportController::class, 'index'])->name('reports');
        // AJAX endpoint for fetching products by factory
        Route::get('/factories/{factory}/products', [\App\Http\Controllers\Wholesaler\OrderController::class, 'getFactoryProducts'])->name('factories.products');
        Route::get('products/edit', [\App\Http\Controllers\Wholesaler\ProductController::class, 'editPrices'])->name('products.edit');
        Route::post('products/update-prices', [\App\Http\Controllers\Wholesaler\ProductController::class, 'updatePrices'])->name('products.updatePrices');
        Route::post('/retailer-orders/{order}/approve', [App\Http\Controllers\Wholesaler\RetailerOrderController::class, 'approve'])->name('retailer_orders.approve');
        Route::post('/retailer-orders/{order}/reject', [App\Http\Controllers\Wholesaler\RetailerOrderController::class, 'reject'])->name('retailer_orders.reject');
        // Wholesaler products page (moved here)
        Route::get('/products', [\App\Http\Controllers\Wholesaler\ProductController::class, 'index'])->name('products');
    });

    // Workforce Manager routes
    // Route::prefix('workforce_manager')->middleware('role:workforce_manager')->name('workforce_manager.')->group(function () {
    //     Route::get('/dashboard', [WorkforceManagerDashboardController::class, 'index'])->name('dashboard');
    //     // Workforce management modules
    // });

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
