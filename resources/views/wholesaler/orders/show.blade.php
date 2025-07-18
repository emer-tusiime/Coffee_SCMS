@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Order #{{ $order->id }}</h1>
    <p><strong>Factory:</strong> {{ $order->factory->name ?? 'N/A' }}</p>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Order Date:</strong> {{ $order->order_date ?? ($order->created_at ? $order->created_at->format('Y-m-d H:i:s') : 'N/A') }}</p>
    <p><strong>Delivery Date:</strong> {{ $order->estimated_delivery_date ? \Illuminate\Support\Carbon::parse($order->estimated_delivery_date)->format('Y-m-d') : 'N/A' }}</p>
    <p><strong>Delivery Address:</strong> {{ $order->delivery_address ?? 'N/A' }}</p>

    <h3>Products</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>UGX {{ number_format($item->price, 0) }}</td>
                    <td>UGX {{ number_format($item->price * $item->quantity, 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No products found for this order.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <a href="{{ route('wholesaler.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
</div>
@endsection 