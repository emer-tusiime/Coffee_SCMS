@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Place a New Order to Wholesaler</h2>
    <form method="POST" action="{{ route('retailer.makeOrder.store') }}" id="orderForm">
        @csrf
        <div class="mb-3">
            <label for="wholesaler_id" class="form-label">Select Wholesaler</label>
            <select name="wholesaler_id" id="wholesaler_id" class="form-control" required>
                <option value="">Select a wholesaler</option>
                @foreach($wholesalers as $wholesaler)
                    <option value="{{ $wholesaler->id }}">{{ $wholesaler->name }} ({{ $wholesaler->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="product_id" class="form-label">Select Product</label>
            <select id="product_id" class="form-control" disabled required>
                <option value="">Select a product</option>
            </select>
        </div>
        <div class="mb-3 row">
            <div class="col-md-4">
                <input type="number" id="quantity" class="form-control" placeholder="Quantity" min="1" value="1" disabled>
            </div>
            <div class="col-md-4">
                <input type="text" id="unit_price" class="form-control" placeholder="Unit Price" readonly>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-success w-100" id="addToCart" disabled>Add to Cart</button>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">Cart</div>
            <div class="card-body">
                <table class="table" id="cartTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="cartEmptyRow">
                            <td colspan="5" class="text-center">No items in cart</td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-2">Subtotal: UGX <span id="cartSubtotal">0.00</span></div>
            </div>
        </div>
        <div class="mb-3">
            <label for="delivery_date" class="form-label">Estimated Delivery Date</label>
            <input type="date" name="delivery_date" id="delivery_date" class="form-control" required>
        </div>
        <input type="hidden" name="cart_json" id="cart_json">
        <button type="submit" class="btn btn-primary"><i class="fas fa-shopping-cart me-1"></i> Place Order</button>
    </form>
</div>
<script>
    let productsByWholesaler = {};
    @foreach($wholesalers as $wholesaler)
        productsByWholesaler[{{ $wholesaler->id }}] = @json($wholesalerInventories[$wholesaler->id] ?? []);
    @endforeach
    let cart = [];
    function updateProductDropdown() {
        const wholesalerId = document.getElementById('wholesaler_id').value;
        const productSelect = document.getElementById('product_id');
        productSelect.innerHTML = '<option value="">Select a product</option>';
        if (productsByWholesaler[wholesalerId] && productsByWholesaler[wholesalerId].length > 0) {
            productsByWholesaler[wholesalerId].forEach(product => {
                // Use wholesaler_price if available
                productSelect.innerHTML += `<option value="${product.id}" data-price="${product.wholesaler_price}">${product.name}</option>`;
            });
            productSelect.disabled = false;
        } else {
            productSelect.disabled = true;
        }
        document.getElementById('quantity').disabled = true;
        document.getElementById('addToCart').disabled = true;
        document.getElementById('unit_price').value = '';
    }
    document.getElementById('wholesaler_id').addEventListener('change', updateProductDropdown);
    document.getElementById('product_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const price = selected.getAttribute('data-price');
        document.getElementById('unit_price').value = price || '';
        document.getElementById('quantity').disabled = !this.value;
        document.getElementById('addToCart').disabled = !this.value;
    });
    document.getElementById('addToCart').addEventListener('click', function() {
        const wholesalerId = document.getElementById('wholesaler_id').value;
        const productSelect = document.getElementById('product_id');
        const productId = productSelect.value;
        const productName = productSelect.options[productSelect.selectedIndex].text;
        const price = parseFloat(document.getElementById('unit_price').value);
        const quantity = parseInt(document.getElementById('quantity').value);
        if (!productId || !quantity || quantity < 1) return;
        const existing = cart.find(item => item.product_id == productId);
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push({ product_id: productId, product_name: productName, price, quantity });
        }
        renderCart();
    });
    function renderCart() {
        const tbody = document.querySelector('#cartTable tbody');
        tbody.innerHTML = '';
        let subtotal = 0;
        if (cart.length === 0) {
            tbody.innerHTML = '<tr id="cartEmptyRow"><td colspan="5" class="text-center">No items in cart</td></tr>';
        } else {
            cart.forEach((item, idx) => {
                const total = item.price * item.quantity;
                subtotal += total;
                tbody.innerHTML += `<tr><td>${item.product_name}</td><td>${item.quantity}</td><td>${item.price}</td><td>${total}</td><td><button type='button' class='btn btn-danger btn-sm' onclick='removeCartItem(${idx})'>Remove</button></td></tr>`;
            });
        }
        document.getElementById('cartSubtotal').innerText = subtotal.toFixed(2);
        document.getElementById('cart_json').value = JSON.stringify(cart);
    }
    window.removeCartItem = function(idx) {
        cart.splice(idx, 1);
        renderCart();
    }
    // Reset cart and product dropdown on wholesaler change
    document.getElementById('wholesaler_id').addEventListener('change', function() {
        cart = [];
        renderCart();
    });
    // On form submit, ensure cart is not empty
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            alert('Please add at least one product to the cart.');
            e.preventDefault();
        }
    });
</script>
@endsection 