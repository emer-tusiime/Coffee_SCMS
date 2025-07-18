@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Order #{{ $order->id }}</h5>
                    <div>
                        <a href="{{ route('retailer.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Orders
                        </a>
                        @if(!$order->status)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                            <i class="fas fa-times me-1"></i> Cancel Order
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Factory:</strong>
                                        @if($order->factory)
                                            {{ $order->factory->name }}
                                        @elseif($order->wholesaler)
                                            {{ $order->wholesaler->name }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                    <p><strong>Order Date:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</strong></p>
                                    <p><strong>Order To:</strong>
                                        @if($order->factory)
                                            {{ $order->factory->name }}
                                        @elseif($order->wholesaler)
                                            {{ $order->wholesaler->name }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                    <p><strong>Status:</strong> <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ $order->status === 'completed' ? 'Completed' : 'Pending' }}</span></p>
                                    <p><strong>Delivery Status:</strong> <span class="badge bg-{{ $order->delivery_status_badge }}">
                                        {{ ucfirst($order->delivery_status) }}</span></p>
                                    <p><strong>Estimated Delivery:</strong> {{ $order->estimated_delivery_date ? $order->estimated_delivery_date->format('Y-m-d') : 'N/A' }}</p>
                                    <p><strong>Total Value:</strong> UGX {{ number_format($order->items->sum(function($item) { return ($item->price ?? 0) * $item->quantity; }), 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit Price (UGX)</th>
                                            <th>Total (UGX)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>UGX {{ number_format($item->price ?? 0, 2) }}</td>
                                            <td>UGX {{ number_format(($item->price ?? 0) * $item->quantity, 2) }}</td>
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
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="cancelOrderForm" action="{{ route('retailer.orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="reason">Reason for Cancellation</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="cancelOrderForm" class="btn btn-danger">Cancel Order</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.rating-input {
    display: flex;
    gap: 5px;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input label {
    font-size: 24px;
    color: #ccc;
    cursor: pointer;
}

.rating-input input[type="radio"]:checked ~ label {
    color: #ffd700;
}

.rating-input label:hover,
.rating-input label:hover ~ label {
    color: #ffd700;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill delivery date
    const deliveryDate = document.getElementById('delivery_date');
    if (deliveryDate) {
        deliveryDate.value = new Date().toISOString().split('T')[0];
    }

    // Handle order cancellation
    const cancelOrderForm = document.getElementById('cancelOrderForm');
    if (cancelOrderForm) {
        cancelOrderForm.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to cancel this order?')) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush
@endsection
