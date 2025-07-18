<!DOCTYPE html>
<html>
<head>
    <title>Retailer Dashboard Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Retailer Dashboard Test</h1>
    
    <h2>Authentication Test</h2>
    @if(auth()->check())
        <p class="success">✓ User is authenticated</p>
        <p>User ID: {{ auth()->id() }}</p>
        <p>User Email: {{ auth()->user()->email }}</p>
        <p>User Role: {{ auth()->user()->getRoleNames()->first() ?? 'No role assigned' }}</p>
    @else
        <p class="error">✗ No user is authenticated</p>
    @endif

    <h2>Database Test</h2>
    @php
        try {
            $userCount = \DB::table('users')->count();
            echo "<p class='success'>✓ Database connection successful!</p>";
            echo "<p>Total users: $userCount</p>";
            
            // Test if retailer role exists
            $retailerRole = \Spatie\Permission\Models\Role::where('name', 'retailer')->first();
            if ($retailerRole) {
                echo "<p class='success'>✓ Retailer role exists</p>";
            } else {
                echo "<p class='error'>✗ Retailer role does not exist</p>";
            }
            
        } catch (\Exception $e) {
            echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
        }
    @endphp

    <h2>Session Data</h2>
    <pre>{{ print_r(session()->all(), true) }}</pre>

    <h2>Environment</h2>
    <p>Laravel: {{ app()->version() }}</p>
    <p>PHP: {{ phpversion() }}</p>
</body>
</html>
