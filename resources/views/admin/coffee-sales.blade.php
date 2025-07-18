@extends('layouts.dashboard')

@section('title', 'Coffee Sales Management - Coffee SCMS')

@section('dashboard-title')
    <i class="fas fa-coffee me-2"></i>Coffee Sales Management
@endsection

@section('dashboard-content')
    <!-- Quick Stats -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ number_format($totalSales) }} kg</h3>
                    <p>Total Coffee Purchased</p>
                </div>
                <div class="icon">
                    <i class="fas fa-weight"></i>
                </div>
                <a href="{{ route('coffee-sales.analytics') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ number_format($totalValue) }}</h3>
                    <p>Total Value</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('coffee-sales.analytics') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>{{ $pendingSales->total() }}</h3>
                    <p>Pending Approvals</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Review Below <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>{{ $approvedSales->count() }}</h3>
                    <p>Recent Approvals</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="#" class="small-box-footer">
                    View Below <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Pending Sales -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clock me-2"></i>Pending Coffee Sales Approvals
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Quantity (kg)</th>
                            <th>Price/kg</th>
                            <th>Quality Grade</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingSales as $sale)
                        <tr>
                            <td>{{ $sale->supplier->name }}</td>
                            <td>{{ number_format($sale->quantity, 2) }} kg</td>
                            <td>{{ number_format($sale->price_per_kg, 2) }}</td>
                            <td>{{ $sale->quality_grade }}</td>
                            <td>{{ $sale->created_at->format('Y-m-d') }}</td>
                            <td>
                                <form action="{{ route('coffee-sales.approve', $sale) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this coffee sale?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('coffee-sales.reject', $sale) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this coffee sale?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pendingSales->links() }}
            </div>
        </div>
    </div>

    <!-- Recent Approved Sales -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-check-circle me-2"></i>Recent Approved Sales
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Quantity (kg)</th>
                            <th>Total Value</th>
                            <th>Quality Grade</th>
                            <th>Approval Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedSales as $sale)
                        <tr>
                            <td>{{ $sale->supplier->name }}</td>
                            <td>{{ number_format($sale->quantity, 2) }} kg</td>
                            <td>{{ number_format($sale->total_value, 2) }}</td>
                            <td>{{ $sale->quality_grade }}</td>
                            <td>{{ $sale->updated_at->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
