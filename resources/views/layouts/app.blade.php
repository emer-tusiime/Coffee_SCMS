<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Coffee SCMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('styles')

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar .nav-link {
            font-weight: 500;
            padding: 0.75rem 1.5rem;
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
        }
    </style>
</head>
<body>
    @if(!Auth::check())
        <style>
            body {
                background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80') no-repeat center center fixed;
                background-size: cover;
                min-height: 100vh;
                position: relative;
            }
            .guest-overlay {
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(60, 40, 20, 0.35);
                z-index: 0;
            }
            .guest-center {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                position: relative;
                z-index: 1;
            }
            .guest-logo {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                background: #fff;
                object-fit: cover;
                box-shadow: 0 2px 8px rgba(0,0,0,0.10);
                margin-bottom: 1.5rem;
            }
            .guest-welcome {
                color: #fff;
                font-size: 2rem;
                font-weight: 700;
                text-align: center;
                margin-bottom: 2rem;
                letter-spacing: 1px;
                text-shadow: 0 2px 8px rgba(60,40,20,0.18);
            }
        </style>
        <div class="guest-overlay"></div>
        <div class="guest-center">
            <img src="/images/coffee.png" alt="Coffee SCMS Logo" class="guest-logo">
            <div class="guest-welcome">Welcome to Coffee SCMS</div>
            <div style="width:100%; max-width:420px;">
                @yield('content')
            </div>
        </div>
    @elseif(Auth::check() && Auth::user()->role === 'supplier')
        <style>
            .coffee-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 230px;
                background: linear-gradient(180deg, #6f4e37 0%, #a67c52 100%);
                color: #fff;
                z-index: 2000;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-top: 2rem;
                box-shadow: none !important;
                border-right: none !important;
            }
            body {
                background: #f8f9fa !important;
            }
            .coffee-sidebar .logo {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #fff;
                object-fit: cover;
                box-shadow: 0 2px 8px rgba(0,0,0,0.10);
                margin-bottom: 1rem;
            }
            .coffee-sidebar .app-name {
                font-size: 1.4rem;
                font-weight: 700;
                letter-spacing: 1px;
                margin-bottom: 2.5rem;
            }
            .coffee-sidebar .nav {
                width: 100%;
            }
            .coffee-sidebar .nav-link {
                color: #fff;
                font-size: 1.08rem;
                font-weight: 500;
                padding: 0.85rem 2rem;
                border-radius: 12px 0 0 12px;
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                transition: background 0.18s;
            }
            .coffee-sidebar .nav-link i {
                margin-right: 0.8rem;
                font-size: 1.2rem;
            }
            .coffee-sidebar .nav-link.active, .coffee-sidebar .nav-link:hover {
                background: rgba(255,255,255,0.13);
                color: #ffe0b2;
            }
            .coffee-sidebar .sidebar-bottom {
                margin-top: auto;
                margin-bottom: 2rem;
            }
            .main-content {
                flex: 1;
                padding-left: 250px;
                padding-top: 0;
            }
            @media (max-width: 768px) {
                .main-content {
                    padding-left: 0;
                }
                .coffee-sidebar {
                    width: 100vw;
                    position: relative;
                    height: auto;
                }
            }
        </style>
        <div style="display: flex; min-height: 100vh;">
            <div class="coffee-sidebar">
                <img src="/images/coffee.png" alt="Coffee SCMS Logo" class="logo">
                <div class="app-name">Coffee SCMS</div>
                <nav class="nav flex-column w-100">
                    <a href="/dashboard" class="nav-link"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
                    <a href="{{ route('supplier.products.index') }}" class="nav-link"><i class="fa fa-box"></i> Products</a>
                    <a href="/supplier/orders" class="nav-link"><i class="fa fa-shopping-cart"></i> Factory Orders</a>
                </nav>
                <div class="sidebar-bottom w-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link w-100" style="background:rgba(255,255,255,0.10); color:#fff;"><i class="fa fa-sign-out-alt"></i> Logout</button>
                    </form>
                </div>
            </div>
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    @elseif(Auth::check() && Auth::user()->role === 'customer')
        <style>
            .coffee-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 230px;
                background: linear-gradient(180deg, #6f4e37 0%, #a67c52 100%);
                color: #fff;
                z-index: 2000;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-top: 2rem;
                box-shadow: none !important;
                border-right: none !important;
            }
            body {
                background: #f8f9fa !important;
            }
            .coffee-sidebar .logo {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #fff;
                object-fit: cover;
                box-shadow: 0 2px 8px rgba(0,0,0,0.10);
                margin-bottom: 1rem;
            }
            .coffee-sidebar .app-name {
                font-size: 1.4rem;
                font-weight: 700;
                letter-spacing: 1px;
                margin-bottom: 2.5rem;
            }
            .coffee-sidebar .nav {
                width: 100%;
            }
            .coffee-sidebar .nav-link {
                color: #fff;
                font-size: 1.08rem;
                font-weight: 500;
                padding: 0.85rem 2rem;
                border-radius: 12px 0 0 12px;
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                transition: background 0.18s;
            }
            .coffee-sidebar .nav-link i {
                margin-right: 0.8rem;
                font-size: 1.2rem;
            }
            .coffee-sidebar .nav-link.active, .coffee-sidebar .nav-link:hover {
                background: rgba(255,255,255,0.13);
                color: #ffe0b2;
            }
            .coffee-sidebar .sidebar-bottom {
                margin-top: auto;
                margin-bottom: 2rem;
            }
        </style>
        <div class="coffee-sidebar">
            <img src="/images/coffee.png" alt="Coffee SCMS Logo" class="logo">
            <div class="app-name">Coffee SCMS</div>
            <nav class="nav flex-column w-100">
                <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"><i class="fa fa-home"></i> Dashboard</a>
                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#makeOrderModal"><i class="fa fa-mug-hot"></i> Make Order</a>
                <a href="{{ route('customer.orders.index') }}" class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}"><i class="fa fa-box"></i> My Orders</a>
                <a href="#" class="nav-link"><i class="fa fa-user"></i> Profile</a>
            </nav>
            <div class="sidebar-bottom w-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-100" style="background:rgba(255,255,255,0.10); color:#fff;"><i class="fa fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
        <div style="min-height:100vh;">
            @yield('content')
        </div>
    @elseif(Auth::check() && Auth::user()->role === 'retailer')
        <style>
            .coffee-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 250px;
                background: #2c3e50;
                color: #fff;
                z-index: 1000;
                padding-top: 20px;
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }
            .coffee-content {
                margin-left: 250px;
                padding: 20px;
            }
            .sidebar-header {
                padding: 10px 20px;
                margin-bottom: 20px;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            .sidebar-menu {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            .sidebar-menu li a {
                display: block;
                padding: 12px 20px;
                color: #ecf0f1;
                text-decoration: none;
                transition: all 0.3s;
            }
            .sidebar-menu li a:hover,
            .sidebar-menu li a.active {
                background: #34495e;
                color: #3498db;
            }
            .sidebar-menu li a i {
                margin-right: 10px;
                width: 20px;
                text-align: center;
            }
            .main-header {
                background: #fff;
                padding: 15px 20px;
                border-bottom: 1px solid #e0e0e0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .user-menu {
                display: flex;
                align-items: center;
            }
            .user-menu img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                margin-right: 10px;
            }
        </style>
        
        <div class="d-flex">
            <!-- Sidebar -->
            <div class="coffee-sidebar">
                <div class="sidebar-header">
                    <h4 class="mb-0">Retailer Panel</h4>
                    <small class="text-muted">Coffee SCMS</small>
                </div>
                
                <ul class="sidebar-menu">
                    <li><a href="{{ route('retailer.dashboard') }}" class="{{ request()->is('retailer/dashboard*') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="{{ route('retailer.orders.create') }}" class="{{ request()->routeIs('retailer.orders.create') ? 'active' : '' }}"><i class="fas fa-plus"></i> Make Order</a></li>
                    <li><a href="{{ route('retailer.orders.index') }}" class="{{ request()->is('retailer/orders*') ? 'active' : '' }}"><i class="fas fa-shopping-cart"></i> View Orders</a></li>
                    <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="flex-grow-1">
                <header class="main-header">
                    <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
                    <div class="user-menu">
                        <span class="me-3">{{ Auth::user()->name }}</span>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" alt="User">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>
                
                <main class="coffee-content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </main>
            </div>
        </div>

    @elseif(Auth::check() && Auth::user()->role === 'factory')
        <style>
            .coffee-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 230px;
                background: linear-gradient(180deg, #4e342e 0%, #a67c52 100%);
                color: #fff;
                z-index: 2000;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-top: 2rem;
                box-shadow: none !important;
                border-right: none !important;
            }
            .main-content {
                margin-left: 230px;
                min-height: 100vh;
                padding: 2rem 2rem 2rem 2rem;
                background: #f8f9fa;
            }
        </style>
        <div class="coffee-sidebar">
            <div class="app-name">Coffee SCMS</div>
            <nav class="nav flex-column w-100">
                <a href="{{ route('factory.dashboard') }}" class="nav-link {{ request()->routeIs('factory.dashboard') ? 'active' : '' }}"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
                <a href="{{ route('factory.production.lines') }}" class="nav-link {{ request()->routeIs('factory.production.lines') ? 'active' : '' }}"><i class="fa fa-industry"></i> Production Lines</a>
            </nav>
            <div class="sidebar-bottom w-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-100" style="background:rgba(255,255,255,0.10); color:#fff;"><i class="fa fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
        <div class="main-content">
            @yield('content')
        </div>
    @elseif(Auth::check() && Auth::user()->role === 'wholesaler')
        <style>
            .coffee-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 230px;
                background: linear-gradient(180deg, #795548 0%, #a67c52 100%);
                color: #fff;
                z-index: 2000;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding-top: 2rem;
            }
            .main-content {
                margin-left: 230px;
                min-height: 100vh;
                padding: 2rem;
                background: #f8f9fa;
            }
        </style>
        <div class="coffee-sidebar">
            {{-- Removed the image as per user request --}}
            <div class="app-name">Coffee SCMS</div>
            <nav class="nav flex-column w-100">
                <a href="{{ route('wholesaler.dashboard') }}" class="nav-link"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
                <a href="{{ route('wholesaler.orders.create') }}" class="nav-link"><i class="fa fa-plus"></i> Make an Order</a>
                <a href="{{ route('wholesaler.orders.index') }}" class="nav-link"><i class="fa fa-shopping-cart"></i> Orders</a>
                <a href="{{ route('wholesaler.retailer.orders.index') }}" class="nav-link"><i class="fa fa-users"></i> Retailer Orders</a>
                <a href="{{ route('wholesaler.reports') }}" class="nav-link"><i class="fa fa-chart-bar"></i> Reports</a>
            </nav>
            <div class="sidebar-bottom w-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-100" style="background:rgba(255,255,255,0.10); color:#fff;">
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        <div class="main-content">
            @yield('content')
        </div>
    @endif

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>
