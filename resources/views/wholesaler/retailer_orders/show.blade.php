@extends('layouts.dashboard_wholesaler')

@section('title', 'Retailer Order Details')

@section('content')
<div class="container-fluid">
    <h1>Retailer Order #{{ $order->id }}</h1>
    <div class="card mt-4">
        <div class="card-body">
            <p><strong>Retailer:</strong> {{ $order->retailer->name ?? 'N/A' }}</p>
            <p><strong>Status:</strong>
                @if($order->status === 'approved')
                    <span class="badge bg-success">Approved</span>
                @elseif($order->status === 'pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                @elseif($order->status === 'rejected')
                    <span class="badge bg-danger">Rejected</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                @endif
            </p>
            <p><strong>Created At:</strong> {{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <h5 class="mt-4">Order Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price (UGX)</th>
                            <th>Total (UGX)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>UGX {{ number_format($item->price ?? 0, 2) }}</td>
                            <td>UGX {{ number_format(($item->price ?? 0) * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-3"><strong>Total Value:</strong> UGX {{ number_format($order->items->sum(function($item) { return ($item->price ?? 0) * $item->quantity; }), 2) }}</p>
            @if($order->status === 'pending')
                <div class="mt-4">
                    <form action="{{ route('retailer_orders.approve', $order->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-success">Approve</button>
                    </form>
                    <form action="{{ route('retailer_orders.reject', $order->id) }}" method="POST" style="display:inline-block; margin-left: 10px;">
                        @csrf
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 