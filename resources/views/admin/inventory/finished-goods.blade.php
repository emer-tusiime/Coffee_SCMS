@extends('layouts.dashboard')

@section('title', 'Finished Goods')

@section('dashboard-title')
    <h1>Finished Goods (Out of Stock)</h1>
@endsection

@section('dashboard-content')
    <div class="card">
        <div class="card-header">All Out-of-Stock Raw Products by Suppliers</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Supplier</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($finishedGoods as $product)
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