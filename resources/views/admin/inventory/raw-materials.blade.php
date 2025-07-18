@extends('layouts.dashboard')

@section('title', 'Raw Materials')

@section('dashboard-title')
    <h1>Raw Materials</h1>
@endsection

@section('dashboard-content')
    <div class="card">
        <div class="card-header">
            <form method="GET" class="form-inline">
                <label for="supplier_id" class="mr-2">Select Supplier:</label>
                <select name="supplier_id" id="supplier_id" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $selectedSupplierId == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body">
            @if($selectedSupplier)
                <div class="mb-3">
                    <strong>Supplier:</strong> {{ $selectedSupplier->name }}<br>
                    <strong>Total Products:</strong> {{ $totalProducts }}<br>
                    <strong>Total Stock:</strong> {{ $totalStock }}<br>
                    <strong>Total Stock Value:</strong> UGX {{ number_format($totalStockValue, 0) }}
                </div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Supplier</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rawProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $product->stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection 