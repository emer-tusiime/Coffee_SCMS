@extends('layouts.dashboard_wholesaler')

@section('title', 'Wholesaler Dashboard')

@section('content')
<div class="container-fluid">
    @if(isset($debug))
        <div class="alert alert-success">{{ $debug }}</div>
    @endif
    <div class="mb-4">
        <h1 class="h2">Wholesaler Dashboard</h1>
        <p class="text-muted">Welcome back, {{ Auth::user()->name }}! Here you can view products from factories and manage your orders.</p>
    </div>
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Products in Stock</h5>
                    <h2 class="card-text">{{ $totalStock }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Pending Orders</h5>
                    <h2 class="card-text">{{ $pendingOrders->count() }}</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Action Buttons -->
    <div class="row mb-4">
        <div class="col-md-4">
            <a href="{{ route('wholesaler.orders.create') }}" class="btn btn-primary btn-lg w-100 mb-2">+ Make an Order</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('wholesaler.orders.index') }}" class="btn btn-outline-primary btn-lg w-100 mb-2">View All Orders</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('wholesaler.products.edit') }}" class="btn btn-outline-info btn-lg w-100 mb-2">Edit Products</a>
        </div>
    </div>
    <!-- Recent Orders Table -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Recent Orders</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Factory</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->factory->name ?? 'N/A' }}</td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @if($order->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($order->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($order->status === 'delivered')
                                        <span class="badge bg-success">Delivered</span>
                                    @elseif($order->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                                <td><a href="{{ route('wholesaler.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No recent orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection