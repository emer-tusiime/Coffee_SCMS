@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Orders to Suppliers</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="orderTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Pending Orders</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">Rejected Orders</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered" type="button" role="tab">Delivered Orders</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="orderTabsContent">
                        <!-- Pending Orders -->
                        <div class="tab-pane fade show active" id="pending" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Supplier</th>
                                            <th>Products</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingOrders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                            <td>
                                                @foreach($order->items as $item)
                                                    <span class="badge bg-secondary">{{ $item->product->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>UGX {{ number_format($order->items->sum('total'), 0) }}</td>
                                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No pending orders</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Rejected Orders -->
                        <div class="tab-pane fade" id="rejected" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Supplier</th>
                                            <th>Products</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rejectedOrders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                            <td>
                                                @foreach($order->items as $item)
                                                    <span class="badge bg-secondary">{{ $item->product->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>UGX {{ number_format($order->items->sum('total'), 0) }}</td>
                                            <td><span class="badge bg-danger">Rejected</span></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No rejected orders</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Delivered Orders -->
                        <div class="tab-pane fade" id="delivered" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Supplier</th>
                                            <th>Products</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($deliveredOrders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                            <td>
                                                @foreach($order->items as $item)
                                                    <span class="badge bg-secondary">{{ $item->product->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>UGX {{ number_format($order->items->sum('total'), 0) }}</td>
                                            <td><span class="badge bg-success">Delivered</span></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No delivered orders</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 