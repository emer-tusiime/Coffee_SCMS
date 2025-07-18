@extends('layouts.dashboard_wholesaler')

@section('title', 'Retailer Orders')

@section('content')
<div class="container-fluid">
    <h1>Retailer Orders</h1>
    <div class="card mt-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="retailerOrderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All Orders</button>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content" id="retailerOrderTabsContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Retailer</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($retailerOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->retailer->name ?? 'N/A' }}</td>
                                <td>
                                    @if($order->status === 'delivered')
                                        <span class="badge bg-success">Delivered</span>
                                    @elseif($order->status === 'approved')
                                        <span class="badge bg-primary">Approved</span>
                                    @elseif($order->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($order->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at ? $order->created_at->format('Y-m-d') : '' }}</td>
                                <td>
                                    <a href="{{ route('wholesaler.retailer_orders.show', $order->id) }}" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No retailer orders.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 