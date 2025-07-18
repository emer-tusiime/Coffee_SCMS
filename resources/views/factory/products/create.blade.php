@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fa fa-plus"></i> Add New Product</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('factory.products.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                        <input type="number" id="stock" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" id="price" name="price" class="form-control" value="{{ old('price', $product->price ?? 0) }}" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="1" @selected(old('status', 1) == 1)>Active</option>
                            <option value="0" @selected(old('status', 1) == 0)>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Save Product</button>
                    <a href="{{ route('factory.products.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 