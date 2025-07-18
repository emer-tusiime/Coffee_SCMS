@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Processed Goods Marketplace</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Factory</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->description }}</td>
                                    <td>{{ $product->factory->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($product->price, 2) }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <!-- Order Button -->
                                        <form action="{{ route('retailer.orders.store') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="number" name="quantity" min="1" max="{{ $product->stock }}" value="1" style="width: 60px;">
                                            <button type="submit" class="btn btn-sm btn-success">Order</button>
                                        </form>
                                        <!-- Complain Button -->
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#complainModal{{ $product->id }}">Complain</button>
                                        <!-- Complain Modal -->
                                        <div class="modal fade" id="complainModal{{ $product->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Complain about {{ $product->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('retailer.complaints.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <input type="hidden" name="factory_id" value="{{ $product->factory_id }}">
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="complaint_message_{{ $product->id }}" class="form-label">Complaint</label>
                                                                <textarea name="message" id="complaint_message_{{ $product->id }}" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Submit Complaint</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
</div>
@endsection 