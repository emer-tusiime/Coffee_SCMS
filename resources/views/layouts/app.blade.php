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
                box-shadow: 2px 0 16px rgba(111, 78, 55, 0.10);
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
                <a href="#overview" class="nav-link"><i class="fa fa-home"></i> Overview</a>
                <a href="{{ route('supplier.application.status') }}" class="nav-link"><i class="fa fa-file-alt"></i> Vendor Application</a>
            </nav>
            <div class="sidebar-bottom w-100">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                    <button type="submit" class="nav-link w-100" style="background:rgba(255,255,255,0.10); color:#fff;"><i class="fa fa-sign-out-alt"></i> Logout</button>
                                    </form>
            </div>
        </div>
        <div style="margin-left:230px; min-height:100vh;">
            @yield('content')
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
                box-shadow: 2px 0 16px rgba(111, 78, 55, 0.10);
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
        <div style="margin-left:230px; min-height:100vh;">
        @yield('content')
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
                box-shadow: 2px 0 16px rgba(78, 52, 46, 0.10);
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
                <a href="{{ route('factory.dashboard') }}" class="nav-link {{ request()->routeIs('factory.dashboard') ? 'active' : '' }}"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
                <a href="{{ route('factory.production.lines') }}" class="nav-link {{ request()->routeIs('factory.production.lines') ? 'active' : '' }}"><i class="fa fa-industry"></i> Production Lines</a>
                <a href="{{ route('factory.quality.control') }}" class="nav-link {{ request()->routeIs('factory.quality.control') ? 'active' : '' }}"><i class="fa fa-check-circle"></i> Quality Control</a>
                <a href="{{ route('factory.maintenance') }}" class="nav-link {{ request()->routeIs('factory.maintenance') ? 'active' : '' }}"><i class="fa fa-tools"></i> Maintenance</a>
                <a href="{{ route('factory.workforce') }}" class="nav-link {{ request()->routeIs('factory.workforce') ? 'active' : '' }}"><i class="fa fa-users"></i> Workforce</a>
            </nav>
            <div class="sidebar-bottom w-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-100" style="background:rgba(255,255,255,0.10); color:#fff;"><i class="fa fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
        <div style="margin-left:230px; min-height:100vh;">
            @yield('content')
        </div>
    @endif

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>
