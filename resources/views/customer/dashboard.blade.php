@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        background: #f7f3ef;
    }
    .hero-coffee {
        background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80') no-repeat center center;
        background-size: cover;
        border-radius: 0 0 32px 32px;
        padding: 3rem 2rem 6rem 2rem;
        color: #fff;
        position: relative;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
    }
    .hero-coffee .overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(60, 40, 20, 0.65);
        border-radius: 0 0 32px 32px;
        z-index: 1;
    }
    .hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }
    .hero-content h1 {
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }
    .hero-content p {
        font-size: 1.2rem;
        color: #ffe0b2;
        margin-bottom: 0;
    }
    .fab-make-order {
        position: fixed;
        bottom: 32px;
        right: 32px;
        z-index: 1050;
        background: linear-gradient(90deg, #6f4e37 0%, #a67c52 100%);
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 70px;
        height: 70px;
        box-shadow: 0 4px 24px rgba(111, 78, 55, 0.18);
        font-size: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .fab-make-order:hover {
        background: linear-gradient(90deg, #a67c52 0%, #6f4e37 100%);
        box-shadow: 0 8px 32px rgba(111, 78, 55, 0.25);
        color: #fff;
    }
    .coffee-card {
        background: #fff8f0;
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(111, 78, 55, 0.10);
        padding: 2rem 2rem 1.5rem 2rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        text-align: center;
        padding: 2rem 1rem;
        border-radius: 18px;
        background: #fff8f0;
        box-shadow: 0 2px 12px rgba(111, 78, 55, 0.10);
        margin-bottom: 1.5rem;
    }
    .stat-card i {
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
    }
    .order-status-badge {
        font-size: 0.95em;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.25em 0.75em;
    }
    .order-status-pending { background: #ffe082; color: #6f4e37; }
    .order-status-processing { background: #b3e5fc; color: #0277bd; }
    .order-status-completed { background: #c8e6c9; color: #388e3c; }
    .empty-state {
        text-align: center;
        color: #bdbdbd;
        padding: 2rem 0;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        color: #ffe0b2;
    }
</style>
<div class="hero-coffee mb-5">
    <div class="overlay"></div>
    <div class="hero-content">
        <h1>Welcome, {{ Auth::user()->name }}!</h1>
        <p>Manage your coffee orders, track your deliveries, and enjoy the best supply chain experience.</p>
    </div>
</div>

<!-- Floating Make Order Button -->
<button class="fab-make-order" data-bs-toggle="modal" data-bs-target="#makeOrderModal" title="Make Order">
    <i class="fa fa-mug-hot"></i>
</button>

<div class="container">
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <i class="fa fa-clipboard-list text-brown"></i>
                <div class="fw-bold mt-2">Total Orders</div>
                <div class="fs-3 fw-bold">{{ $orderStats['total'] }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <i class="fa fa-hourglass-half text-warning"></i>
                <div class="fw-bold mt-2">Pending Orders</div>
                <div class="fs-3 fw-bold text-warning">{{ $orderStats['pending'] }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <i class="fa fa-cogs text-info"></i>
                <div class="fw-bold mt-2">Processing Orders</div>
                <div class="fs-3 fw-bold text-info">{{ $orderStats['processing'] }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <i class="fa fa-check-circle text-success"></i>
                <div class="fw-bold mt-2">Completed Orders</div>
                <div class="fs-3 fw-bold text-success">{{ $orderStats['completed'] }}</div>
            </div>
        </div>
    </div>

    <!-- Pending Orders Section -->
    <div class="coffee-card mb-4">
        <h4 class="mb-3"><i class="fa fa-clock text-warning me-2"></i>Pending Orders</h4>
        @php $pendingOrders = $orders->where('status', 'pending'); @endphp
        @if($pendingOrders->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td><span class="order-status-badge order-status-pending">Pending</span></td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td><a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fa fa-coffee"></i>
                <div>No pending orders.</div>
            </div>
        @endif
        </div>

    <!-- Recent Orders Section -->
    <div class="coffee-card">
        <h4 class="mb-3"><i class="fa fa-history text-brown me-2"></i>Recent Orders</h4>
            @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                            </tr>
                        </thead>
                    <tbody>
                            @foreach($orders as $order)
                            <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="order-status-badge order-status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td><a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
            <div class="empty-state">
                <i class="fa fa-box-open"></i>
                <div>No orders found.</div>
            </div>
            @endif
        </div>
</div>

<!-- Make Order Modal (UI only, wire up backend logic as needed) -->
<div class="modal fade" id="makeOrderModal" tabindex="-1" aria-labelledby="makeOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="makeOrderModalLabel"><i class="fa fa-plus me-2"></i>Make a New Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- TODO: Wire up with backend logic -->
        <form id="makeOrderForm" method="POST" action="#">
          @csrf
          <div class="mb-3">
            <label for="productSelect" class="form-label">Select Product</label>
            <select class="form-select" id="productSelect" name="product_id">
              <option value="">Choose a product</option>
              <!-- TODO: Populate with products from backend -->
            </select>
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1">
          </div>
          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane me-1"></i> Place Order</button>
          </div>
        </form>
      </div>
        </div>
    </div>
</div>
@endsection
