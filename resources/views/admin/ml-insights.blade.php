@extends('layouts.dashboard')

@section('title', 'ML Insights')

@section('dashboard-title')
    <h1>Machine Learning Insights</h1>
@endsection

@section('dashboard-content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Top Selling Products (Supplier Level)</div>
            <div class="card-body">
                <canvas id="topSupplierProductsChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Top Selling Products (Factory Level)</div>
            <div class="card-body">
                <canvas id="topFactoryProductsChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Top Selling Products (Wholesaler Level)</div>
            <div class="card-body">
                <canvas id="topWholesalerProductsChart"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Future Demand (Supplier Top Product: <b>{{ $topSupplierProduct->product->name ?? 'N/A' }}</b>)</div>
            <div class="card-body">
                <canvas id="futureSupplierPredictionChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Future Demand (Factory Top Product: <b>{{ $topFactoryProduct->product->name ?? 'N/A' }}</b>)</div>
            <div class="card-body">
                <canvas id="futureFactoryPredictionChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">Future Demand (Wholesaler Top Product: <b>{{ $topWholesalerProduct->product->name ?? 'N/A' }}</b>)</div>
            <div class="card-body">
                <canvas id="futureWholesalerPredictionChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Top Selling Products Charts
    new Chart(document.getElementById('topSupplierProductsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topSupplierProducts->pluck('product.name')) !!},
            datasets: [{
                label: 'Total Sold',
                data: {!! json_encode($topSupplierProducts->pluck('total_quantity')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        }
    });
    new Chart(document.getElementById('topFactoryProductsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topFactoryProducts->pluck('product.name')) !!},
            datasets: [{
                label: 'Total Sold',
                data: {!! json_encode($topFactoryProducts->pluck('total_quantity')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.5)'
            }]
        }
    });
    new Chart(document.getElementById('topWholesalerProductsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topWholesalerProducts->pluck('product.name')) !!},
            datasets: [{
                label: 'Total Sold',
                data: {!! json_encode($topWholesalerProducts->pluck('total_quantity')) !!},
                backgroundColor: 'rgba(255, 206, 86, 0.5)'
            }]
        }
    });
    // Future Demand Prediction Charts
    new Chart(document.getElementById('futureSupplierPredictionChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($futureSupplierPrediction['dates'] ?? []) !!},
            datasets: [{
                label: 'Predicted Sales',
                data: {!! json_encode($futureSupplierPrediction['predicted'] ?? []) !!},
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.3
            }]
        }
    });
    new Chart(document.getElementById('futureFactoryPredictionChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($futureFactoryPrediction['dates'] ?? []) !!},
            datasets: [{
                label: 'Predicted Sales',
                data: {!! json_encode($futureFactoryPrediction['predicted'] ?? []) !!},
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
                tension: 0.3
            }]
        }
    });
    new Chart(document.getElementById('futureWholesalerPredictionChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($futureWholesalerPrediction['dates'] ?? []) !!},
            datasets: [{
                label: 'Predicted Sales',
                data: {!! json_encode($futureWholesalerPrediction['predicted'] ?? []) !!},
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                fill: true,
                tension: 0.3
            }]
        }
    });
});
</script>
@endpush 