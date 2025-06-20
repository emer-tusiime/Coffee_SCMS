@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#overview">
                            <i class="fas fa-home me-2"></i> Overview
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ route('supplier.products.index') }}">
                            <i class="fas fa-box me-2"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('supplier.inventory.index') }}">
                            <i class="fas fa-warehouse me-2"></i> Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('supplier.orders.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                        </a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('supplier.application.status') }}">
                            <i class="fas fa-file-alt me-2"></i> Vendor Application
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
                Welcome, {{ Auth::user()->name }}! This is your supplier dashboard. Use the Vendor Application link to manage your application or contact support for help.
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card text-dark bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Vendor Application Status</h5>
                            <p class="card-text">Check the status of your vendor application or submit a new one.</p>
                            <a href="{{ route('supplier.application.status') }}" class="btn btn-primary">View Application</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-dark bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Need Help?</h5>
                            <p class="card-text">Contact support for assistance with your supplier account.</p>
                            <a href="mailto:support@coffee-scms.com" class="btn btn-outline-secondary">Contact Support</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overview Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Products</h5>
                            <p class="card-text h2">{{ $totalProducts ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Active Orders</h5>
                            <p class="card-text h2">{{ $activeOrders ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Low Stock Items</h5>
                            <p class="card-text h2">{{ $lowStockItems ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Pending Messages</h5>
                            <p class="card-text h2">{{ $pendingMessages ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Recent Orders</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders ?? [] as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->products_count }} items</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No recent orders</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Analytics Preview -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Sales Trend</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Inventory Status</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Recent Messages</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentMessages ?? [] as $message)
                        <a href="{{ route('supplier.chat.show', $message->conversation_id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $message->sender_name }}</h5>
                                <small>{{ $message->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($message->content, 100) }}</p>
                        </a>
                        @empty
                        <p class="text-center">No recent messages</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesChartData['labels'] ?? []) !!},
            datasets: [{
                label: 'Sales',
                data: {!! json_encode($salesChartData['data'] ?? []) !!},
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
    });

    // Inventory Chart
    const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
    new Chart(inventoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($inventoryChartData['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($inventoryChartData['data'] ?? []) !!},
                backgroundColor: [
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)',
                    'rgb(255, 99, 132)'
                ]
            }]
        }
    });
</script>
@endpush
@endsection
