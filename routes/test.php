<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/test-retailer', function () {
    try {
        // Test database connection
        DB::connection()->getPdo();
        
        // Get a retailer user
        $retailer = User::role('retailer')->first();
        
        if (!$retailer) {
            return 'No retailer user found';
        }
        
        // Test authentication
        auth()->login($retailer);
        
        // Test role assignment
        if (!auth()->user()->hasRole('retailer')) {
            return 'User does not have retailer role';
        }
        
        // Test dashboard view
        $view = view('retailer.dashboard', [
            'stats' => [
                'total_orders' => 0,
                'completed_orders' => 0,
                'pending_orders' => 0,
                'total_products' => 0
            ],
            'orders' => collect([]),
            'lowStockProducts' => collect([])
        ]);
        
        return $view->render();
        
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
    }
});
