@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>My Products</h4>
                        <a href="{{ route('supplier.products.create') }}" class="btn btn-primary">Add New Product</a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr @if($product->stock <= $product->threshold) style="background-color: #fff3cd;" @endif>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ Str::limit($product->description, 50) }}</td>
                                        <td>{{ number_format($product->price, 2) }}</td>
                                        <td>
                                            {{ $product->stock }}
                                            @if($product->stock <= $product->threshold)
                                                <span class="badge bg-warning text-dark">Low Stock!</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('supplier.products.edit', $product) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('supplier.products.destroy', $product) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="alert alert-info">
                <strong>Total Products:</strong> {{ $products->count() }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-success">
                <strong>Total Stock Value:</strong> UGX {{ number_format($products->sum(function($product) { return $product->stock * $product->price; }), 0) }}
            </div>
        </div>
    </div>
</div>
@endsection
