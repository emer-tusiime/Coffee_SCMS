@extends('layouts.dashboard_admin')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="fa fa-plus"></i> Add New Product</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                        <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                        <input type="number" id="stock" name="stock" class="form-control" value="{{ old('stock', 0) }}" min="0" required>
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" id="image" name="image" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="active" class="form-label">Status</label>
                        <select id="active" name="active" class="form-select">
                            <option value="1" @selected(old('active', 1) == 1)>Active</option>
                            <option value="0" @selected(old('active', 1) == 0)>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Save Product</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection