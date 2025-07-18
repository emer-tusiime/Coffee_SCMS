@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Quick Stats -->
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Total Quantity Sold</h5>
                    <p class="card-text h2">{{ number_format($totalQuantity) }} kg</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Total Value</h5>
                    <p class="card-text h2">{{ number_format($totalValue) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Pending Approvals</h5>
                    <p class="card-text h2">{{ $sales->where('approved', false)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Sales History</h5>
                    <a href="{{ route('supplier.coffee-sales.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Report New Sale
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Quantity (kg)</th>
                                    <th>Price/kg</th>
                                    <th>Quality Grade</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                <tr>
                                    <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                                    <td>{{ number_format($sale->quantity, 2) }} kg</td>
                                    <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                                    <td>{{ $sale->quality_grade }}</td>
                                    <td>{{ number_format($sale->total_value, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $sale->status_color }}">
                                            {{ $sale->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $sales->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
