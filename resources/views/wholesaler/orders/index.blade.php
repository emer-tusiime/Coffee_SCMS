@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Wholesaler Orders</h1>
        <a href="{{ route('wholesaler.orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Place New Order
        </a>
    </div>
    <hr>
    <ul class="nav nav-tabs mb-3" id="orderTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Pending Orders</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered" type="button" role="tab">Delivered Orders</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">Rejected Orders</button>
        </li>
    </ul>
    <div class="alert alert-info">
        <strong>Debug:</strong> Current User ID: {{ Auth::id() }}
    </div>
    <div class="tab-content" id="orderTabsContent">
        <!-- Pending Orders -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            @if(isset($pendingOrders) && count($pendingOrders))
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Factory</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->factory && $order->factory->name ? $order->factory->name : '-' }}</td>
                                <td>
                                    @foreach($order->items as $item)
                                        <span class="badge bg-secondary">{{ $item->product->name }}</span>
                                    @endforeach
                                </td>
                                <td>UGX {{ number_format($order->items->sum('price'), 0) }}</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td><a href="{{ route('wholesaler.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No pending orders.</p>
            @endif
        </div>
        <!-- Delivered Orders -->
        <div class="tab-pane fade" id="delivered" role="tabpanel">
            @if(isset($deliveredOrders) && count($deliveredOrders))
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Factory</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveredOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->factory && $order->factory->name ? $order->factory->name : '-' }}</td>
                                <td>
                                    @foreach($order->items as $item)
                                        <span class="badge bg-secondary">{{ $item->product->name }}</span>
                                    @endforeach
                                </td>
                                <td>UGX {{ number_format($order->items->sum('price'), 0) }}</td>
                                <td><span class="badge bg-success">Delivered</span></td>
                                <td><a href="{{ route('wholesaler.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No delivered orders.</p>
            @endif
        </div>
        <!-- Rejected Orders -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            @if(isset($rejectedOrders) && count($rejectedOrders))
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Factory</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rejectedOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->factory && $order->factory->name ? $order->factory->name : '-' }}</td>
                                <td>
                                    @foreach($order->items as $item)
                                        <span class="badge bg-secondary">{{ $item->product->name }}</span>
                                    @endforeach
                                </td>
                                <td>UGX {{ number_format($order->items->sum('price'), 0) }}</td>
                                <td><span class="badge bg-danger">Rejected</span></td>
                                <td><a href="{{ route('wholesaler.orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No rejected orders.</p>
            @endif
        </div>
    </div>
</div>
@endsection 