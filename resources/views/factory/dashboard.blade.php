@extends('layouts.app')

@section('content')
<div class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar -->
    <nav class="coffee-sidebar sidebar min-vh-100">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('factory.dashboard') ? 'active' : '' }}" href="{{ route('factory.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('factory.suppliers.index') ? 'active' : '' }}" href="{{ route('factory.suppliers.index') }}">
                        <i class="fas fa-users"></i> Suppliers
                        </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('factory.products.index') ? 'active' : '' }}" href="{{ route('factory.products.index') }}">
                        <i class="fas fa-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('factory.orders.create') ? 'active' : '' }}" href="{{ route('factory.orders.create') }}">
                        <i class="fas fa-plus"></i> Make Order to Supplier
                        </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('factory.orders.index') ? 'active' : '' }}" href="{{ route('factory.orders.index') }}">
                        <i class="fas fa-clipboard-list"></i> Supplier Orders
                            </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('factory.wholesaler.orders') ? 'active' : '' }}" href="{{ route('factory.wholesaler.orders') }}">
                        <i class="fas fa-users"></i> Wholesaler Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                        <i class="fa fa-user"></i> Profile
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start" style="background:rgba(255,255,255,0.10); color:#fff; border:none;">
                            <i class="fa fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Main Content -->
    <main class="flex-grow-1 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Factory Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Print</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                        <i class="fas fa-calendar"></i>
                        This week
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100 shadow">
                        <div class="card-body">
                        <h5 class="card-title">Total Products in Stock</h5>
                        <h2 class="card-text">{{ $stats['total_products_in_stock'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100 shadow">
                        <div class="card-body">
                            <h5 class="card-title">Active Products</h5>
                        <h2 class="card-text">{{ $stats['active_products'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark h-100 shadow">
                        <div class="card-body">
                        <h5 class="card-title">Pending Supplier Orders</h5>
                        <h2 class="card-text">{{ $stats['pending_supplier_orders'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-3">
                <div class="card bg-danger text-white h-100 shadow">
                        <div class="card-body">
                        <h5 class="card-title">Pending Wholesaler Orders</h5>
                        <h2 class="card-text">{{ $stats['pending_wholesaler_orders'] ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Stats -->
            <div class="row mb-4">
                <div class="col-md-6">
                <div class="card h-100 shadow">
                    <div class="card-header bg-gradient bg-light">
                            <h5 class="card-title mb-0">Suppliers</h5>
                        </div>
                        <div class="card-body">
                            <h2 class="card-title">{{ $stats['supplier_count'] }}</h2>
                            <p class="card-text">Total registered suppliers</p>
                            <div class="mt-3">
                            <a href="{{ route('factory.suppliers.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-1"></i> View All Suppliers
                            </a>
                            <a href="{{ route('factory.orders.index') }}" class="btn btn-success ms-2">
                                <i class="fas fa-plus me-1"></i> Make Order to Supplier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 shadow d-flex align-items-center justify-content-center">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-4">Quick Actions</h5>
                        <a href="{{ route('factory.inventory.index') }}" class="btn btn-info mb-2 w-75">View Inventory</a><br>
                        <a href="{{ route('factory.orders.index') }}" class="btn btn-primary mb-2 w-75">Make Order to Supplier</a><br>
                        <a href="{{ route('factory.wholesaler.orders') }}" class="btn btn-warning mb-2 w-75">View Wholesaler Orders</a>
                    </div>
                </div>
            </div>
                        </div>
        <!-- Add more premium dashboard content here as needed -->
        </main>
</div>
@endsection

@push('styles')
<style>
.coffee-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 230px;
    background: linear-gradient(180deg, #232526 0%, #414345 100%);
    color: #fff;
    z-index: 2000;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 2rem;
    box-shadow: 2px 0 8px rgba(0,0,0,0.10);
    border-right: none !important;
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
</style>
@endpush
