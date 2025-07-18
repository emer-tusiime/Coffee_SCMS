@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Order Details (Order #{{ $order->id }})</h1>
    <hr>
    <h4>Status: <span class="badge bg-info">{{ ucfirst($order->status) }}</span></h4>
    <h5>Wholesaler: {{ $order->wholesaler ? $order->wholesaler->name : '-' }}</h5>
    <h5>Order Date: {{ $order->created_at->format('Y-m-d') }}</h5>
    <h5>Factory: {{ $order->factory ? $order->factory->name : '-' }}</h5>
    <hr>
    <h4>Order Items</h4>
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
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product ? $item->product->name : '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h4>Total Amount: UGX {{ number_format($order->total_amount, 2) }}</h4>
    <hr>
    <h4>Suggested Products</h4>
    <ul>
        @foreach($suggestedProducts as $product)
            <li>{{ $product->name }} (UGX {{ number_format($product->price, 2) }})</li>
        @endforeach
    </ul>
    <a href="{{ route('factory.wholesaler.orders') }}" class="btn btn-secondary">Back to Orders</a>
</div>
@endsection 