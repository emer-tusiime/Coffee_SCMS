@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Create New Order</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('retailer.orders.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delivery_address">Delivery Address</label>
                                    <textarea class="form-control @error('delivery_address') is-invalid @enderror" 
                                              id="delivery_address" name="delivery_address" rows="3" required>
                                        {{ old('delivery_address') }}
                                    </textarea>
                                    @error('delivery_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delivery_date">Delivery Date</label>
                                    <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" 
                                           id="delivery_date" name="delivery_date" 
                                           min="{{ now()->format('Y-m-d') }}" required>
                                    @error('delivery_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Order Items</h5>
                                        <button type="button" class="btn btn-sm btn-primary float-end" id="addProduct">
                                            <i class="fas fa-plus"></i> Add Product
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="productsContainer">
                                            @if(old('products'))
                                                @foreach(old('products') as $index => $product)
                                                    @include('retailer.orders._product_row', ['index' => $index])
                                                @endforeach
                                            @else
                                                @include('retailer.orders._product_row', ['index' => 0])
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Create Order
                            </button>
                            <a href="{{ route('retailer.orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = {{ old('products') ? count(old('products')) : 1 }};

    document.getElementById('addProduct').addEventListener('click', function() {
        const container = document.getElementById('productsContainer');
        const template = document.getElementById('productRowTemplate').innerHTML;
        container.innerHTML += template.replace(/__index__/g, productIndex++);
    });

    // Initialize delivery date picker
    const deliveryDate = document.getElementById('delivery_date');
    if (deliveryDate) {
        deliveryDate.value = new Date().toISOString().split('T')[0];
    }
});
</script>
@endpush
@endsection
