<div class="product-row mb-3">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="products[__index__][product_id]">Product</label>
                <select class="form-control" id="products[__index__][product_id]" name="products[__index__][product_id]" required>
                    <option value="" selected>Select Product</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="products[__index__][quantity]">Quantity (kg)</label>
                <input type="number" class="form-control" id="products[__index__][quantity]" 
                       name="products[__index__][quantity]" min="0" step="0.01" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="products[__index__][notes]">Notes</label>
                <input type="text" class="form-control" id="products[__index__][notes]" 
                       name="products[__index__][notes]">
            </div>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger mt-4 remove-product">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const removeButtons = document.querySelectorAll('.remove-product');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.product-row').remove();
        });
    });
});
</script>
