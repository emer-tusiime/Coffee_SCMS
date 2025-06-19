@extends('layouts.dashboard_admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fa fa-warehouse"></i> Wholesaler Dashboard</h2>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text fs-2 fw-bold">{{ $productsCount ?? 0 }}</p>
                    <a href="{{ route('wholesaler.products.index') }}" class="btn btn-primary btn-sm">View Products</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Orders</h5>
                    <p class="card-text fs-2 fw-bold">{{ $ordersCount ?? 0 }}</p>
                    <a href="{{ route('wholesaler.orders.index') }}" class="btn btn-success btn-sm">View Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Pending Deliveries</h5>
                    <p class="card-text fs-2 fw-bold">{{ $pendingDeliveries ?? 0 }}</p>
                    <a href="{{ route('wholesaler.orders.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm">View Pending</a>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <h4>Recent Orders</h4>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order</th>
                        <th>Retailer</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->retailer->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection