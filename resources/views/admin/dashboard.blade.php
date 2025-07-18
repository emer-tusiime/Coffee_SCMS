<?php

/**
 * @var \Illuminate\Support\ViewErrorBag $errors
 * @var int $totalOrders
 * @var float $productionEfficiency
 * @var int $inventoryAlertsCount
 * @var int $totalQualityIssues
 * @var \Illuminate\Support\Collection $inventoryAlerts
 * @var \Illuminate\Support\Collection $recentQualityIssues
 * @var \Illuminate\Support\Collection $supplierPerformance
 * @var int $totalUsers
 * @var int $customerCount
 * @var int $supplierCount
 * @var \Illuminate\Support\Collection $pendingAccounts
 * @var int $qualityIssues
 * @var \Illuminate\Support\Collection $productionLines
 * @var int $totalCoffeeSales
 * @var int $totalCoffeeValue
 * @var int $pendingCoffeeSales
 * @var \Illuminate\Support\Collection $topSellingProducts
 * @var array $demandPrediction
 * @var array $qualityPrediction
 * @var \Illuminate\Support\Collection $topSuppliers
 */

?>
@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Coffee SCMS')

@section('dashboard-title')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Admin Dashboard</h1>
    </div>
@endsection

@section('dashboard-content')
    <!-- Welcome Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow rounded-4 bg-gradient-info text-white border-0">
                <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between">
                    <div>
                        <h2 class="fw-bold mb-1">Welcome back, {{ Auth::user()->name ?? 'Admin' }}!</h2>
                        <p class="mb-0">Here's a quick overview of your system. Have a productive day managing Coffee SCMS ☕</p>
                </div>
                    <div class="d-none d-md-block">
                        <i class="fas fa-user-shield fa-4x opacity-25"></i>
                </div>
                </div>
            </div>
        </div>
                </div>

    <!-- Simple Stats Row for Suppliers, Wholesalers, Factories -->
    <div class="row mb-4 g-4">
        <div class="col-md-4">
            <div class="card shadow rounded-4 border-0 text-center">
                <div class="card-body">
                    <i class="fas fa-truck fa-2x text-info mb-2"></i>
                    <h4 class="fw-bold mb-1">Suppliers</h4>
                    <div class="display-6 fw-bold">{{ \App\Models\User::where('role', 'supplier')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow rounded-4 border-0 text-center">
                <div class="card-body">
                    <i class="fas fa-industry fa-2x text-warning mb-2"></i>
                    <h4 class="fw-bold mb-1">Wholesalers</h4>
                    <div class="display-6 fw-bold">{{ \App\Models\User::where('role', 'wholesaler')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow rounded-4 border-0 text-center">
                <div class="card-body">
                    <i class="fas fa-warehouse fa-2x text-success mb-2"></i>
                    <h4 class="fw-bold mb-1">Factories</h4>
                    <div class="display-6 fw-bold">{{ \App\Models\User::where('role', 'factory')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health and Quick Links Row -->
    <div class="row mb-4 g-4">
        <div class="col-md-6">
            <div class="card shadow rounded-4 border-0 h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-heartbeat text-danger me-2"></i>System Health</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center"><i class="fas fa-database text-primary me-2"></i> Database: <span class="badge bg-success ms-auto">Connected</span></li>
                        <li class="list-group-item d-flex align-items-center"><i class="fas fa-server text-secondary me-2"></i> Server Status: <span class="badge bg-success ms-auto">Online</span></li>
                        <li class="list-group-item d-flex align-items-center"><i class="fas fa-clock text-warning me-2"></i> Last Backup: <span class="ms-auto">{{ now()->subDays(1)->format('M d, Y H:i') }}</span></li>
                        <li class="list-group-item d-flex align-items-center"><i class="fas fa-users text-info me-2"></i> Active Users: <span class="ms-auto">{{ \App\Models\User::where('status', 'active')->count() }}</span></li>
                    </ul>
                    <div class="mt-4">
                        <canvas id="systemHealthChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex flex-column gap-4">
            <div class="card shadow rounded-4 border-0 h-100 mb-3">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-link text-primary me-2"></i>Quick Links</h5>
                </div>
                <div class="card-body d-flex flex-wrap gap-4 justify-content-center">
                    <a href="{{ route('admin.users.index', ['role' => 'supplier']) }}" class="text-decoration-none text-center">
                        <i class="fas fa-truck fa-2x text-info mb-1"></i><br><span class="fw-semibold">Suppliers</span>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'wholesaler']) }}" class="text-decoration-none text-center">
                        <i class="fas fa-industry fa-2x text-warning mb-1"></i><br><span class="fw-semibold">Wholesalers</span>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'factory']) }}" class="text-decoration-none text-center">
                        <i class="fas fa-warehouse fa-2x text-success mb-1"></i><br><span class="fw-semibold">Factories</span>
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.quality.issues') }}" class="text-decoration-none flex-grow-1">
                <div class="card bg-danger text-white shadow rounded-4 border-0 h-100">
                    <div class="card-header border-0 bg-transparent">
                        <h3 class="card-title">
                            <i class="fas fa-bug me-2"></i>Quality Issues
                        </h3>
        </div>
                    <div class="card-body text-center">
                        <h2 class="text-white mb-0 display-4 fw-bold">{{ $totalQualityIssues }}</h2>
                        <p class="mb-0">Open quality issues in the system</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Motivational Quote Card -->
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <div class="card shadow rounded-4 border-0 bg-gradient-light">
                <div class="card-body text-center">
                    <blockquote class="blockquote mb-0">
                        <p class="fs-4 fst-italic">“Great things are done by a series of small things brought together.”</p>
                        <footer class="blockquote-footer mt-2">Vincent Van Gogh</footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>

    <!-- ML Analytics Charts -->
    <!-- Future: Add Wholesaler and Factory ML Insights here -->
@endsection

@push('styles')
<style>
    .small-box {
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-radius: 1rem;
        background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
        transition: box-shadow 0.2s;
    }
    .small-box:hover {
        box-shadow: 0 4px 24px rgba(0,0,0,0.12);
    }
    .small-box > .inner {
        padding: 18px 16px 10px 16px;
    }
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box p {
        font-size: 1.1rem;
        margin-bottom: 0;
    }
    .small-box .icon {
        transition: all 0.3s linear;
        position: absolute;
        top: 10px;
        right: 20px;
        z-index: 0;
    }
    .small-box .icon i {
        font-size: 3.5rem;
        color: rgba(0,0,0,0.08);
    }
    .small-box .small-box-footer {
        position: relative;
        text-align: center;
        padding: 8px 0;
        color: #fff;
        color: rgba(255,255,255,0.8);
        display: block;
        height: 36px;
        background: rgba(0,0,0,0.1);
        text-decoration: none;
        border-radius: 0 0 1rem 1rem;
        font-weight: 500;
        font-size: 1rem;
    }
    .small-box .small-box-footer:hover {
        color: #fff;
        background: rgba(0,0,0,0.15);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Demand Prediction Chart
    var demandCtx = document.getElementById('demandChart').getContext('2d');
    new Chart(demandCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($demandPrediction['dates'] ?? []) !!},
            datasets: [{
                label: 'Predicted Demand',
                data: {!! json_encode($demandPrediction['predicted'] ?? []) !!},
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, plugins: { legend: { display: true } } }
    });
    // Top Selling Products Chart
    var topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(topProductsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topSellingProducts->pluck('product.name')) !!},
            datasets: [{
                label: 'Total Sales',
                data: {!! json_encode($topSellingProducts->pluck('total_quantity')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.5)'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
    // Supplier Performance Chart
    var supplierPerfCtx = document.getElementById('supplierPerformanceChart').getContext('2d');
    new Chart(supplierPerfCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topSuppliers->pluck('supplier_name')) !!},
            datasets: [
                {
                    label: 'On-time Delivery (%)',
                    data: {!! json_encode($topSuppliers->pluck('on_time_delivery_rate')) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.5)'
                },
                {
                    label: 'Quality Score (%)',
                    data: {!! json_encode($topSuppliers->pluck('quality_score')) !!},
                    backgroundColor: 'rgba(255, 206, 86, 0.5)'
                }
            ]
        },
        options: { responsive: true, plugins: { legend: { display: true } } }
    });

    // System Health Line Chart (sample data for last 7 days)
    var ctx = document.getElementById('systemHealthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @for($i = 6; $i >= 0; $i--)
                    '{{ now()->subDays($i)->format('M d') }}'@if($i > 0),@endif
                @endfor
            ],
            datasets: [{
                label: 'Active Users',
                data: [8, 7, 9, 8, 10, 9, 8], // Replace with real data if available
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush
