@if(!isset($products) || $products->isEmpty())
    <div class="alert alert-warning">No products available for ordering.</div>
@else
<div class="row g-2 align-items-end product-row" data-index="{{ $index }}">
    <div class="col-md-5">
        <div class="form-group">
            <label for="products[{{ $index }}][product_id]">Product</label>
            <select name="products[{{ $index }}][product_id]" class="form-control" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        @if(old('products.' . $index . '.product_id') == $product->id)
                            selected
                        @elseif(isset($selectedProduct) && $selectedProduct && $selectedProduct->id == $product->id && $index == 0)
                            selected
                        @endif
                    >
                        {{ $product->name }} (UGX {{ number_format($product->price, 0) }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="products[{{ $index }}][quantity]">Quantity</label>
            <input type="number" name="products[{{ $index }}][quantity]" class="form-control" min="1" value="{{ old('products.' . $index . '.quantity', 1) }}" required>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Price</label>
            <input type="text" class="form-control" value="" placeholder="Auto" readonly>
        </div>
    </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-danger btn-remove-product" title="Remove"><i class="fas fa-trash"></i></button>
    </div>
</div>
@endif 