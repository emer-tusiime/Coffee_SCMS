@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Place a New Order</h1>
    <hr>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('wholesaler.orders.store') }}" id="orderForm">
        @csrf

        <!-- Factory Selection -->
        <div class="mb-3">
            <label for="factory_id" class="form-label">Select Factory</label>
            <select name="factory_id" id="factory_id" class="form-control" required>
                <option value="">Select a factory</option>
                @foreach($factories as $factory)
                    <option value="{{ $factory->id }}">{{ $factory->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Product Search & Add to Cart -->
        <div class="mb-3 position-relative" style="z-index: 1000;">
            <label class="form-label">Search Product</label>
            <div class="input-group">
                <input type="text" id="product-search" class="form-control" placeholder="Type product name..." autocomplete="off" disabled>
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" id="product-dropdown-btn" tabindex="-1" disabled style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                    <span class="fa fa-chevron-down"></span>
                </button>
            </div>
            <ul class="dropdown-menu w-100" id="product-dropdown-list" style="max-height: 250px; overflow-y: auto;"></ul>
        </div>
        <div class="mb-3 row align-items-end">
            <div class="col-md-6">
                <input type="hidden" id="selected-product-id">
                <input type="text" id="selected-product-price" class="form-control" placeholder="Price" readonly>
            </div>
            <div class="col-md-3">
                <input type="number" id="selected-product-qty" class="form-control" placeholder="Quantity" min="1" value="1" disabled>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-success w-100" id="add-to-cart" disabled>Add to Cart</button>
            </div>
        </div>

        <!-- Cart Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Cart</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Cart items will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold">Subtotal:</span> UGX <span id="subtotal-amount">0.00</span>
                </div>
                <a href="{{ route('wholesaler.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </div>

        <!-- Hidden input for cart data -->
        <input type="hidden" name="products_json" id="products_json">
        <input type="hidden" name="wholesale_price" id="wholesale_price">

        <!-- Delivery Details -->
        <div class="mt-4">
            <div class="mb-3">
                <label for="delivery_address" class="form-label">Delivery Address</label>
                <textarea name="delivery_address" id="delivery_address" class="form-control" required>{{ old('delivery_address') }}</textarea>
            </div>
            <div class="mb-3">
                <label for="delivery_date" class="form-label">Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" required 
                       min="{{ date('Y-m-d') }}" value="{{ old('delivery_date') }}">
            </div>
            <div class="mb-3">
                <label for="payment_terms" class="form-label">Payment Terms</label>
                <select name="payment_terms" id="payment_terms" class="form-control" required>
                    <option value="">Select payment terms</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                    <option value="7 Days">7 Days</option>
                    <option value="14 Days">14 Days</option>
                    <option value="30 Days">30 Days</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="shipping_method" class="form-label">Shipping Method</label>
                <select name="shipping_method" id="shipping_method" class="form-control" required>
                    <option value="">Select shipping method</option>
                    <option value="Standard">Standard</option>
                    <option value="Express">Express</option>
                    <option value="Overnight">Overnight</option>
                </select>
            </div>
        </div>
        <!-- Place Order Button (moved below shipping method) -->
        <div class="mb-4 text-end">
            <button type="submit" class="btn btn-primary" id="place-order-btn" disabled>
                <i class="fas fa-shopping-cart me-2"></i>Place Order
            </button>
        </div>
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .ui-autocomplete {
        z-index: 9999;
    }
    .error-message {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }
    .is-invalid {
        border-color: #dc3545;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function() {
    let products = [];
    let cart = [];
    let subtotal = 0;

    // Factory selection: fetch products
    $('#factory_id').on('change', function() {
        const factoryId = $(this).val();
        resetProductSearch();
        if (factoryId) {
            $('#product-search').prop('disabled', false);
            $('#product-dropdown-btn').prop('disabled', false);
            $.getJSON(`/wholesaler/factories/${factoryId}/products`, function(data) {
                products = data;
                setupAutocomplete();
            });
        } else {
            $('#product-search').prop('disabled', true);
            $('#product-dropdown-btn').prop('disabled', true);
            products = [];
        }
    });

    function setupAutocomplete() {
        $('#product-search').autocomplete({
            source: function(request, response) {
                const term = request.term.toLowerCase();
                response(products.filter(p => p.name.toLowerCase().includes(term)));
            },
            minLength: 1,
            select: function(event, ui) {
                selectProduct(ui.item);
            }
        });
    }

    // Dropdown arrow click: show all products
    $('#product-dropdown-btn').on('click', function(e) {
        e.preventDefault();
        if (products.length === 0) return;
        const dropdown = $('#product-dropdown-list');
        dropdown.empty();
        products.forEach(function(p) {
            dropdown.append(`<li><a href="#" class="dropdown-item product-dropdown-item" data-id="${p.id}" data-name="${p.name}" data-price="${p.price}">${p.name} <span class='text-muted'>(UGX ${parseFloat(p.price).toLocaleString()})</span></a></li>`);
        });
        dropdown.show();
        // Position below input
        const input = $('#product-search');
        dropdown.css({ top: input.outerHeight() + input.position().top, left: input.position().left });
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#product-dropdown-list, #product-dropdown-btn, #product-search').length) {
            $('#product-dropdown-list').hide();
        }
    });

    // Select product from dropdown
    $('#product-dropdown-list').on('click', '.product-dropdown-item', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = $(this).data('price');
        $('#product-search').val(name);
        selectProduct({ id, name, price });
        $('#product-dropdown-list').hide();
    });

    function selectProduct(product) {
        $('#selected-product-id').val(product.id);
        $('#selected-product-price').val(product.price ? 'UGX ' + parseFloat(product.price).toLocaleString() : '');
        $('#selected-product-qty').val(1).prop('disabled', false);
        $('#add-to-cart').prop('disabled', false);
    }

    function resetProductSearch() {
        $('#product-search').val('').prop('disabled', true);
        $('#product-dropdown-btn').prop('disabled', true);
        $('#selected-product-id').val('');
        $('#selected-product-price').val('');
        $('#selected-product-qty').val(1).prop('disabled', true);
        $('#add-to-cart').prop('disabled', true);
        $('#product-dropdown-list').hide();
    }

    // Add to cart
    $('#add-to-cart').on('click', function() {
        const productId = $('#selected-product-id').val();
        const productName = $('#product-search').val();
        const qty = parseInt($('#selected-product-qty').val());
        const product = products.find(p => p.id == productId);
        if (!productId || !product || !qty || qty < 1) return;
        // Check if already in cart
        const existing = cart.find(item => item.product_id == productId);
        if (existing) {
            existing.quantity += qty;
        } else {
            cart.push({ product_id: productId, name: productName, quantity: qty, unit_price: product.price });
        }
        updateCartTable();
        resetProductSearch();
        validateForm();
    });

    // Remove from cart
    $('#cart-table').on('click', '.remove-from-cart', function() {
        const productId = $(this).data('id');
        cart = cart.filter(item => item.product_id != productId);
        updateCartTable();
        validateForm();
    });

    function updateCartTable() {
        const tbody = $('#cart-table tbody');
        tbody.empty();
        subtotal = 0;
        cart.forEach(function(item) {
            const total = item.unit_price * item.quantity;
            subtotal += total;
            tbody.append(`<tr><td>${item.name}</td><td>${item.quantity}</td><td>UGX ${parseFloat(item.unit_price).toLocaleString()}</td><td>UGX ${parseFloat(total).toLocaleString()}</td><td><button type='button' class='btn btn-danger btn-sm remove-from-cart' data-id='${item.product_id}'>Remove</button></td></tr>`);
        });
        $('#subtotal-amount').text(subtotal.toLocaleString());
        $('#products_json').val(JSON.stringify(cart.map(item => ({ product_id: item.product_id, quantity: item.quantity, price: item.unit_price }))));
        $('#wholesale_price').val(subtotal);
    }

    function validateForm() {
        const factorySelected = $('#factory_id').val();
        const cartNotEmpty = cart.length > 0;
        const deliveryAddress = $('#delivery_address').val().trim();
        const deliveryDate = $('#delivery_date').val();
        const paymentTerms = $('#payment_terms').val();
        const shippingMethod = $('#shipping_method').val();
        const allFilled = factorySelected && cartNotEmpty && deliveryAddress && deliveryDate && paymentTerms && shippingMethod;
        $('#place-order-btn').prop('disabled', !allFilled);
    }

    // Validate on input change
    $('#delivery_address, #delivery_date, #payment_terms, #shipping_method, #factory_id').on('input change', validateForm);

    // Form validation on submit
    $('#orderForm').on('submit', function(e) {
        validateForm();
        if ($('#place-order-btn').prop('disabled')) {
            e.preventDefault();
            alert('Please fill in all required fields and add at least one product to the cart.');
            return false;
        }
        $('#products_json').val(JSON.stringify(cart.map(item => ({ product_id: item.product_id, quantity: item.quantity, price: item.unit_price }))));
        $('#wholesale_price').val(subtotal);
    });
});
</script>
@endpush

@endsection
