@extends('layouts.dashboard')

@section('title', 'Sales Analytics')

@section('dashboard-title')
    <h1>Sales Analytics</h1>
@endsection

@section('dashboard-content')
    <div class="card mb-4">
        <div class="card-header">Top Demand by Level</div>
        <div class="card-body text-center">
            <div style="max-width: 400px; margin: 0 auto;">
                <canvas id="demandChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Top Supplier</div>
                <div class="card-body">
                    <p><strong>Product:</strong> {{ $topSupplier->product->name ?? 'N/A' }}</p>
                    <p><strong>Supplier:</strong> {{ $topSupplier->product->supplier->name ?? 'N/A' }}</p>
                    <p><strong>Total Sold:</strong> {{ $topSupplier->total_quantity ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Top Factory</div>
                <div class="card-body">
                    <p><strong>Product:</strong> {{ $topFactory->product->name ?? 'N/A' }}</p>
                    <p><strong>Factory:</strong> {{ $topFactory->product->factory->user->name ?? $topFactory->product->factory->name ?? 'N/A' }}</p>
                    <p><strong>Total Sold:</strong> {{ $topFactory->total_quantity ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Top Wholesaler</div>
                <div class="card-body">
                    <p><strong>Product:</strong> {{ $topWholesaler->product->name ?? 'N/A' }}</p>
                    <p><strong>Wholesaler:</strong> {{ $topWholesaler->order->wholesaler->name ?? 'N/A' }}</p>
                    <p><strong>Total Sold:</strong> {{ $topWholesaler->total_quantity ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('demandChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Total Sold',
                data: {!! json_encode($data) !!},
                backgroundColor: [
                    '#1e3a8a', // deep blue
                    '#b91c1c', // deep red
                    '#f59e42'  // deep gold
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: '#222',
                        font: { size: 16, weight: 'bold' }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            return label + ': ' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush 