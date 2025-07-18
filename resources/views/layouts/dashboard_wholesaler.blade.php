<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Wholesaler Dashboard - Coffee SCMS')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: #fff;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.25rem;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: #007bff;
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        .main-content {
            padding: 20px;
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3" style="width: 250px;">
        <a href="{{ route('wholesaler.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Coffee SCMS</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('wholesaler.dashboard') }}" class="nav-link {{ request()->routeIs('wholesaler.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('wholesaler.orders.create') }}" class="nav-link {{ request()->routeIs('wholesaler.orders.create') ? 'active' : '' }}">
                    <i class="fas fa-plus"></i>
                    Make an Order
                </a>
            </li>
            <li>
                <a href="{{ route('wholesaler.orders.index') }}" class="nav-link {{ request()->routeIs('wholesaler.orders.index') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> View Orders
                </a>
            </li>
            <li>
                <a href="{{ route('wholesaler.orders.index') }}" class="nav-link {{ request()->routeIs('wholesaler.orders.index') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    Orders
                </a>
            </li>
            <li>
                <a href="{{ route('wholesaler.retailer.orders.index') }}" class="nav-link {{ request()->routeIs('wholesaler.retailer.orders.index') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    Retailer Orders
                </a>
            </li>
            <li>
                <a href="{{ route('wholesaler.reports') }}" class="nav-link {{ request()->routeIs('wholesaler.reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    Reports
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle me-2" style="font-size: 1.5rem;"></i>
                <strong>{{ Auth::user()->name }}</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">Sign out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main content -->
    <div class="flex-grow-1">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <button class="btn btn-link" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
            </div>
        </nav>
        
        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            @yield('content')
        </main>
    </div>

    <!-- Required Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('d-none');
        });
    </script>
    @stack('scripts')
</body>
</html>
