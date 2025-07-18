@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Order #{{ $order->id }}</h5>
                    <div>
                        <a href="{{ route('supplier.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Orders
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Factory:</strong> {{ $order->factory->name }}</p>
                                    <p><strong>Order Date:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</strong></p>
                                    <p><strong>Status:</strong> <span class="badge bg-{{ $order->status ? 'success' : 'warning' }}">
                                        {{ $order->status ? 'Accepted' : 'Pending' }}</span></p>
                                    <p><strong>Estimated Delivery:</strong> {{ $order->estimated_delivery_date->format('Y-m-d') }}</p>
                                    <p><strong>Total Value:</strong> UGX {{ number_format($order->items->sum(function($item) { return $item->quantity * $item->price; }), 0) }}</p>
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
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->quantity }} kg</td>
                                            <td>UGX {{ number_format($item->price, 0) }}</td>
                                            <td>UGX {{ number_format($item->quantity * $item->price, 0) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if(!$order->status)
                    <div class="card">
                        <div class="card-body">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success" onclick="handleOrderAction('accept')">
                                    <i class="fas fa-check me-1"></i> Accept Order
                                </button>
                                <button type="button" class="btn btn-danger" onclick="handleOrderAction('reject')">
                                    <i class="fas fa-times me-1"></i> Reject Order
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quality Check Modal -->
<div class="modal fade" id="qualityCheckModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Quality Check</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="qualityCheckForm" action="{{ route('supplier.orders.quality-check', $order->id) }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Quality Grade</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }} kg</td>
                                    <td>
                                        <select class="form-control" name="items[{{ $item->id }}][quality_grade]" required>
                                            <option value="A">A (Excellent)</option>
                                            <option value="B">B (Good)</option>
                                            <option value="C">C (Average)</option>
                                            <option value="D">D (Poor)</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="items[{{ $item->id }}][notes]" placeholder="Any notes about quality...">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="qualityCheckForm" class="btn btn-primary">Save Quality Check</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const orderId = {{ $order->id }};
function handleOrderAction(action) {
    if (!confirm(`Are you sure you want to ${action} this order?`)) {
        return;
    }

    const url = action === 'accept' 
        ? `/supplier/orders/${orderId}/accept`
        : `/supplier/orders/${orderId}/reject`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}
</script>
@endpush
@endsection
