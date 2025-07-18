@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>My Orders</h5>
                    <div>
                        <a href="{{ route('retailer.select.wholesaler') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> New Order
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Orders</h5>
                                    <h2 class="card-text">{{ $pendingOrders->total() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Completed Orders</h5>
                                    <h2 class="card-text">{{ $completedOrders->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        {{--
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Current Month Spend</h5>
                                    <h2 class="card-text">${{ number_format($currentMonthSpend, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        --}}
                    </div>

                    <!-- Pending Orders -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Pending Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
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
                                            <td>
                                                @if($order->factory)
                                                    {{ $order->factory->name }}
                                                @elseif($order->wholesaler)
                                                    {{ $order->wholesaler->name }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }}">
                                                    {{ $order->status === 'completed' ? 'Completed' : 'Pending' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('retailer.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(!$order->status)
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="handleOrderAction('{{ $order->id }}', 'cancel')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $pendingOrders->links() }}
                            </div>
                        </div>
                    </div>

                    <!-- Completed Orders -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Completed Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Factory</th>
                                            <th>Delivery Date</th>
                                            <th>Total Value</th>
                                            <th>Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($completedOrders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->factory->name }}</td>
                                            <td>{{ $order->delivered_at->format('Y-m-d') }}</td>
                                            <td>${{ number_format($order->total_value, 2) }}</td>
                                            <td>
                                                @if($order->rating)
                                                    <div class="rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $order->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                    </div>
                                                @else
                                                    <a href="{{ route('retailer.orders.rate', $order->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-star me-1"></i> Rate Order
                                                    </a>
                                                @endif
                                            </td>
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

@push('scripts')
<script>
function handleOrderAction(orderId, action) {
    if (!confirm(`Are you sure you want to ${action} this order?`)) {
        return;
    }

    const url = action === 'cancel' 
        ? `/retailer/orders/${orderId}/cancel`
        : `/retailer/orders/${orderId}/accept`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: action === 'cancel' ? new FormData(document.querySelector('#cancelOrderForm')) : null,
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
