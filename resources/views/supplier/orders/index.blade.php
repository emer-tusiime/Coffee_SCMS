@extends('layouts.app')

@section('content')
<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Orders</h5>
            <!-- Removed large New Order button -->
        </div>
        <div class="card-body">
            <!-- Pending Orders Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pending Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 270px; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Factory</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->factory->name }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-warning">Pending</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success ms-1" onclick="handleOrderAction({{ $order->id }}, 'accept')">
                                                <i class="fas fa-check"></i> Accept
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger ms-1" onclick="handleOrderAction({{ $order->id }}, 'reject')">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Completed Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Completed Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 270px; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Factory</th>
                                    <th>Delivery Date</th>
                                    <th>Delivery Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedOrders as $order)
                                    @if($order->status)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->factory->name }}</td>
                                        <td>
                                            {{ $order->supplier_accepted_at ? \Carbon\Carbon::parse($order->supplier_accepted_at)->format('Y-m-d') : 'Not available' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Delivered</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('supplier.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function handleOrderAction(orderId, action) {
    if (!confirm(`Are you sure you want to ${action} this order?`)) {
        return;
    }

    const url = `/supplier/orders/${orderId}/${action}`;
    fetch(url, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
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
