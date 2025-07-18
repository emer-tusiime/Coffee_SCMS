<?php

use Illuminate\Support\Facades\Route;

Route::get('/simple-dashboard', function () {
    // Make sure we're logged in as a retailer
    if (!auth()->check()) {
        // Try to log in as a retailer
        $retailer = \App\Models\User::role('retailer')->first();
        if ($retailer) {
            auth()->login($retailer);
        } else {
            return 'No retailer user found in the database';
        }
    }
    
    // Return the simple dashboard view
    return view('retailer.simple-dashboard');
});
