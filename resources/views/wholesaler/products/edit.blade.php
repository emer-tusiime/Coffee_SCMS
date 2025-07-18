@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Product Prices</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('wholesaler.products.updatePrices') }}">
        @csrf
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Factory</th>
                    <th>Default Price</th>
                    <th>Your Price (Editable)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product['name'] }}</td>
                        <td>{{ $product['factory'] }}</td>
                        <td>UGX {{ number_format($product['default_price'], 0) }}</td>
                        <td>
                            <input type="hidden" name="prices[{{ $loop->index }}][product_id]" value="{{ $product['id'] }}">
                            <input type="number" name="prices[{{ $loop->index }}][price]" class="form-control" min="0" step="0.01" value="{{ $product['wholesaler_price'] ?? $product['default_price'] }}" required>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No products found from approved orders.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Save Prices</button>
    </form>
</div>
@endsection 