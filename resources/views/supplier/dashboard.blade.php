@extends('layouts.app')
@section('content')
<style>
    .dashboard-bg-image {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        z-index: 0;
        pointer-events: none;
        opacity: 0.10;
        background: url('/images/coffeeBG.jpg') center center/cover no-repeat;
    }
    .dashboard-main {
        margin-left: 0;
        padding: 2.5rem 2rem 2rem 1.5rem;
        min-height: 100vh;
        position: relative;
        z-index: 1;
    }
    .glass-card {
        background: rgba(255,255,255,0.75);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
        border-radius: 1.5rem;
        padding: 2rem 1.5rem 1.5rem 1.5rem;
        margin-bottom: 2rem;
        transition: box-shadow 0.18s, transform 0.18s;
        border: 1px solid rgba(255,255,255,0.18);
    }
    .glass-card:hover {
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.18);
        transform: translateY(-2px) scale(1.01);
    }
    .dashboard-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        margin-bottom: 2.5rem;
    }
    .dashboard-cards .glass-card {
        flex: 1 1 260px;
        min-width: 260px;
        max-width: 350px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        position: relative;
    }
    .dashboard-cards .glass-card .card-icon {
        font-size: 2.2rem;
        margin-bottom: 0.7rem;
        color: #a67c52;
        opacity: 0.85;
    }
    .dashboard-cards .glass-card .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #6f4e37;
        margin-bottom: 0.2rem;
    }
    .dashboard-cards .glass-card .card-value {
        font-size: 2.2rem;
        font-weight: bold;
        color: #232526;
    }
    .dashboard-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .dashboard-actions .btn {
        font-size: 1.1rem;
        font-weight: 500;
        border-radius: 1rem;
        padding: 0.85rem 2.2rem;
        box-shadow: 0 2px 8px rgba(111, 78, 55, 0.10);
        border: none;
        transition: background 0.18s, color 0.18s, box-shadow 0.18s;
        display: flex;
        align-items: center;
    }
    .dashboard-actions .btn i {
        margin-right: 0.7rem;
        font-size: 1.2rem;
    }
    .dashboard-actions .btn-success {
        background: linear-gradient(90deg, #388e3c 0%, #43a047 100%);
        color: #fff;
    }
    .dashboard-actions .btn-primary {
        background: linear-gradient(90deg, #1976d2 0%, #2196f3 100%);
        color: #fff;
    }
    .dashboard-actions .btn-warning {
        background: linear-gradient(90deg, #fbc02d 0%, #ffd600 100%);
        color: #6f4e37;
    }
    .dashboard-actions .btn-info {
        background: linear-gradient(90deg, #00bcd4 0%, #4dd0e1 100%);
        color: #fff;
    }
    @media (max-width: 900px) {
        .dashboard-main { margin-left: 0; padding: 1.2rem; }
    }
</style>
<div class="dashboard-bg-image"></div>
<div class="dashboard-main">
    <div class="mb-4">
        <h1 class="fw-bold mb-2" style="color:#6f4e37;">Supplier Dashboard</h1>
        <div class="alert alert-info shadow-sm" style="font-size:1.1rem;">
            Welcome, <b>{{ Auth::user()->name }}</b>! Manage your products and orders from factories here.<br>
            <b>Note:</b> Products you add here are accessible by factories for order making.
        </div>
    </div>
    <div class="dashboard-cards">
        <div class="glass-card">
            <div class="card-icon"><i class="fas fa-box"></i></div>
            <div class="card-title">Total Products in Stock</div>
            <div class="card-value">{{ $totalProducts }}</div>
        </div>
        <div class="glass-card">
            <div class="card-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="card-title">Pending Orders from Factories</div>
            <div class="card-value">{{ $pendingOrdersCount }}</div>
        </div>
        <div class="glass-card">
            <div class="card-icon"><i class="fas fa-mug-hot"></i></div>
            <div class="card-title">Fun Coffee Fact</div>
            <div class="card-value" style="font-size:1.1rem; color:#388e3c;">Did you know? Coffee is the second most traded commodity on Earth after oil!</div>
        </div>
    </div>
    <div class="dashboard-actions">
        <a href="{{ route('supplier.products.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Add Product</a>
        <a href="{{ route('supplier.products.index') }}" class="btn btn-primary"><i class="fa fa-box"></i> View Products</a>
        <a href="{{ route('supplier.orders.index') }}" class="btn btn-warning"><i class="fa fa-shopping-cart"></i> View Orders</a>
        <a href="{{ route('supplier.dashboard') }}#stock" class="btn btn-info"><i class="fa fa-warehouse"></i> Manage Stock</a>
    </div>
    <!-- Help Modal Trigger Button -->
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#helpModal"><i class="fa fa-question-circle"></i> Help</button>
</div>
<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="helpModalLabel"><i class="fa fa-question-circle me-2"></i>Supplier Dashboard Help</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul style="font-size:1.08rem;line-height:1.7;">
          <li><b>Dashboard:</b> See an overview of your products and pending orders from factories.</li>
          <li><b>Products:</b> Add, view, and manage your coffee products. These are visible to factories for ordering.</li>
          <li><b>Factory Orders:</b> View and manage orders placed by factories for your products. Accept or reject orders as needed.</li>
          <li><b>Profile:</b> Click your avatar at the top right to view or edit your profile and upload a profile image.</li>
          <li><b>Logout:</b> Use the logout button at the bottom to securely sign out.</li>
        </ul>
        <div class="alert alert-info mt-3 mb-0">
          <b>Tip:</b> Keep your product stock updated to attract more factory orders!
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection
