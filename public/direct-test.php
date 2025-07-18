<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Simple test
echo "<h1>Direct PHP Test</h1>";
echo "<p>If you can see this, PHP is working!</p>";

// Test database connection
try {
    require __DIR__.'/../bootstrap/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "<p>Laravel bootstrapped successfully!</p>";
    
    // Test database connection
    $db = $app->make('db');
    $db->connection()->getPdo();
    echo "<p>Database connection successful!</p>";
    
    // Test user model
    $user = $app->make('App\Models\User');
    $retailer = $user->role('retailer')->first();
    
    if ($retailer) {
        echo "<p>Found retailer: " . htmlspecialchars($retailer->email) . "</p>";
    } else {
        echo "<p>No retailer user found in the database.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>In file: " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
}

// Show PHP info
// echo "<h2>PHP Info</h2>";
// phpinfo();
