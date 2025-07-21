@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('supplier.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('supplier.products.create') }}">
                            <i class="fas fa-plus me-2"></i> Add Product
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">
                            <i class="fas fa-box me-2"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#orders">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.show') }}">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Supplier Dashboard</h1>
            </div>
            <div class="alert alert-info mb-4">
                Welcome, {{ Auth::user()->name }}! Manage your products and orders from factories here.<br>
                <strong>Note:</strong> Products you add here are accessible by factories for order making.
            </div>

            <!-- Supplier Stats -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white shadow h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Products in Stock</h5>
                            <h2 class="card-text">{{ $totalProducts }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('supplier.orders.index') }}#pending" class="text-decoration-none">
                        <div class="card bg-warning text-dark shadow h-100">
                            <div class="card-body">
                                <h5 class="card-title">Pending Orders from Factories</h5>
                                <h2 class="card-text">{{ $pendingOrdersCount }}</h2>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white shadow h-100">
                        <div class="card-body">
                            <h5 class="card-title">Fun Coffee Fact</h5>
                            <p class="card-text">Did you know? Coffee is the second most traded commodity on Earth after oil!</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('supplier.products.create') }}" class="btn btn-lg btn-success w-100 shadow"><i class="fa fa-plus me-2"></i> Add Product</a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('supplier.products.index') }}" class="btn btn-lg btn-primary w-100 shadow"><i class="fa fa-box me-2"></i> View Products</a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('supplier.orders.index') }}" class="btn btn-lg btn-warning w-100 shadow"><i class="fa fa-shopping-cart me-2"></i> View Orders</a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('supplier.dashboard') }}#stock" class="btn btn-lg btn-info w-100 shadow"><i class="fa fa-warehouse me-2"></i> Manage Stock</a>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
