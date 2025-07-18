<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Run the application
$kernel = $app->make(Illine\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Test database connection
try {
    DB::connection()->getPdo();
    echo "Database connection OK!<br>";
} catch (\Exception $e) {
    die("Could not connect to the database. Please check your configuration. Error: " . $e->getMessage());
}

// Test authentication
$user = \App\Models\User::role('retailer')->first();
if ($user) {
    echo "Found retailer user: " . $user->email . "<br>";
    \Illuminate\Support\Facades\Auth::login($user);
    echo "User authenticated: " . (auth()->check() ? 'Yes' : 'No') . "<br>";
    echo "User has role 'retailer': " . ($user->hasRole('retailer') ? 'Yes' : 'No') . "<br>";
} else {
    die("No retailer user found in the database!");
}

// Test view rendering
try {
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
    
    echo "View rendered successfully!<br>";
    echo $view->render();
    
} catch (\Exception $e) {
    die("Error rendering view: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}
