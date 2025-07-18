<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/test-view', function () {
    // Authenticate as a retailer
    $user = User::role('retailer')->first();
    if (!$user) {
        return 'No retailer user found';
    }
    auth()->login($user);
    
    // Return the test view
    return view('retailer.test');
});
