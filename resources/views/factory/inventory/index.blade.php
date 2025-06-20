@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Inventory Management</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Low Stock Alerts</h5>
                            <div class="alert alert-warning">
                                @foreach($lowStock as $item)
                                    <p>{{ $item->product->name }} - {{ $item->quantity }} units remaining</p>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Stock Update</h5>
                            <form action="{{ route('factory.inventory.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="location_id" class="form-select mb-2">
                                            <option value="">Select Location</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="product_id" class="form-select mb-2">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" name="quantity" class="form-control mb-2" placeholder="Quantity">
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary">Update Stock</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventories as $inventory)
                                    <tr>
                                        <td>{{ $inventory->location->name }}</td>
                                        <td>{{ $inventory->product->name }}</td>
                                        <td>{{ $inventory->product->category->name }}</td>
                                        <td>{{ $inventory->quantity }}</td>
                                        <td>{{ $inventory->updated_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
