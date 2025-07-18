@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Your Cart</h2>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('retailer.wholesaler.placeOrder', [$wholesalerId]) }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $items[$product->id]['quantity'] }}</td>
                        <td>UGX {{ number_format($product->price, 0) }}</td>
                        <td>UGX {{ number_format($product->price * $items[$product->id]['quantity'], 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Place Order</button>
    </form>
</div>
@endsection 