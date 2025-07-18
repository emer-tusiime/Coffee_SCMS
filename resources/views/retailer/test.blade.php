<!DOCTYPE html>
<html>
<head>
    <title>Test View</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Test View</h1>
    
    <div>
        <h2>Database Connection:</h2>
        @try
            @php
                DB::connection()->getPdo();
            @endphp
            <p class="success">✓ Database connection successful!</p>
        @catch (Exception $e)
            <p class="error">✗ Database connection failed: {{ $e->getMessage() }}</p>
        @endtry
    </div>

    <div>
        <h2>Authentication:</h2>
        @if(auth()->check())
            <p class="success">✓ User is authenticated as: {{ auth()->user()->email }}</p>
            <p>User has role 'retailer': {{ auth()->user()->hasRole('retailer') ? 'Yes' : 'No' }}</p>
        @else
            <p class="error">✗ No user is authenticated</p>
        @endif
    </div>

    <div>
        <h2>View Data:</h2>
        <pre>{{ print_r(get_defined_vars(), true) }}</pre>
    </div>
</body>
</html>
