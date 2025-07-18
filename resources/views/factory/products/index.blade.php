@extends('layouts.app')

@section('title', 'Manage Products')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 2;
    }
    .product-image {
        height: 180px;
        object-fit: cover;
        width: 100%;
    }
    .product-price {
        font-size: 1.25rem;
        font-weight: 600;
    }
    .sale-price {
        color: #dc3545;
    }
    .original-price {
        text-decoration: line-through;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .inventory-stats {
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    @if(!$factoryExists)
        <div class="alert alert-warning mt-4">You need to complete your factory profile before you can add or manage products.</div>
    @else
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Product Management</h1>
            <a href="{{ route('factory.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Product
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted small mb-1">Total Products</h6>
                                <h3 class="mb-0">{{ $inventorySummary['total_products'] }}</h3>
                            </div>
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted small mb-1">Low Stock</h6>
                                <h3 class="mb-0">{{ $inventorySummary['low_stock'] }}</h3>
                            </div>
                            <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-danger border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted small mb-1">Out of Stock</h6>
                                <h3 class="mb-0">{{ $inventorySummary['out_of_stock'] }}</h3>
                            </div>
                            <div class="icon-shape bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase text-muted small mb-1">Active</h6>
                                <h3 class="mb-0">{{ $inventorySummary['total_products'] - $inventorySummary['out_of_stock'] }}</h3>
                            </div>
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('factory.products.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search Products</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by name...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('factory.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product Table -->
        <div class="card mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>UGX {{ number_format($product->price, 0) }}</td>
                                    <td>{{ $product->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('factory.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit</a>
                                        <form action="{{ route('factory.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No products found. Get started by adding your first product.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize select2
        $('#category').select2({
            placeholder: 'Select a category',
            allowClear: true
        });
        
        // Handle delete confirmation
        let productIdToDelete = null;
        
        window.confirmDelete = function(id) {
            productIdToDelete = id;
            $('#deleteModal').modal('show');
        };
        
        $('#confirmDeleteBtn').click(function() {
            if (productIdToDelete) {
                $(`#delete-form-${productIdToDelete}`).submit();
            }
        });
        
        // Toggle product status
        $('.toggle-status').on('change', function() {
            const productId = $(this).data('id');
            const isActive = $(this).is(':checked');
            
            $.ajax({
                url: `/factory/products/${productId}/toggle-status`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: isActive ? 1 : 0
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'An error occurred');
                    // Revert the toggle on error
                    $(`#toggle-${productId}`).prop('checked', !isActive);
                }
            });
        });
    });
</script>
@endpush