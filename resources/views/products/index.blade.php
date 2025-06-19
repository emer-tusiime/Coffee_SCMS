@extends('layouts.dashboard_admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fa fa-box"></i> Products</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Product</a>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Stock</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $loop->iteration + (($products->currentPage()-1) * $products->perPage()) }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>
                        @if($product->active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection