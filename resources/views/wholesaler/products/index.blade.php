@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Products from Approved Orders</h1>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Factory</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Order #</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product['name'] }}</td>
                    <td>{{ $product['factory'] }}</td>
                    <td>UGX {{ number_format($product['price'], 0) }}</td>
                    <td>{{ $product['quantity'] }}</td>
                    <td>{{ $product['order_id'] }}</td>
                    <td>{{ $product['order_date'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No approved products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 