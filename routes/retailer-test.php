<?php

use App\Http\Controllers\Retailer\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/retailer-test', function () {
    try {
        // Create a new instance of the DashboardController
        $controller = new DashboardController();
        
        // Call the index method directly
        return $controller->index();
    } catch (\Exception $e) {
        // Return the error message if something goes wrong
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
