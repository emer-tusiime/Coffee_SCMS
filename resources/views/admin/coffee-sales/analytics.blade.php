@extends('layouts.dashboard')

@section('title', 'Coffee Sales Analytics - Coffee SCMS')

@section('dashboard-title')
    <i class="fas fa-chart-line me-2"></i>Coffee Sales Analytics
@endsection

@section('dashboard-content')
    <div class="row">
        <!-- Monthly Sales Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i>Monthly Sales Overview
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quality Grade Distribution -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>Quality Grade Distribution
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="qualityGradeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Suppliers -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users me-2"></i>Top Suppliers by Volume
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
                            <th>Total Quantity (kg)</th>
                            <th>Average Price/kg</th>
                            <th>Quality Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topSuppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ number_format($supplier->total_quantity, 2) }} kg</td>
                            <td>{{ number_format($supplier->avg_price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $supplier->quality_rating_color }}">
                                    {{ $supplier->quality_rating }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Sales Chart
    const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(monthlySalesCtx, {
        type: 'line',
        data: {
            labels: @json($monthlySales->pluck('month')->toArray()),
            datasets: [
                {
                    label: 'Quantity (kg)',
                    data: @json($monthlySales->pluck('total_quantity')->toArray()),
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
                },
                {
                    label: 'Value',
                    data: @json($monthlySales->pluck('total_value')->toArray()),
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Quality Grade Chart
    const qualityGradeCtx = document.getElementById('qualityGradeChart').getContext('2d');
    new Chart(qualityGradeCtx, {
        type: 'pie',
        data: {
            labels: @json($qualityGrades->pluck('quality_grade')->toArray()),
            datasets: [{
                data: @json($qualityGrades->pluck('count')->toArray()),
                backgroundColor: [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endpush
