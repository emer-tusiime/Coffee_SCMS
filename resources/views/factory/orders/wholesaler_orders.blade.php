@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>Wholesaler Orders</h1>
    <div class="card mt-4">
        <div class="card-body">
            @if($orders->where('status', 'pending')->count() === 0)
                <p>No pending orders.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Wholesaler</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders->where('status', 'pending') as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->wholesaler && $order->wholesaler->name ? $order->wholesaler->name : '-' }}</td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                                <td>
                                    <a href="{{ route('factory.orders.show', $order->id) }}" class="btn btn-primary btn-sm">View</a>
                                    <form action="{{ route('factory.orders.approve', $order->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                    </form>
                                    <form action="{{ route('factory.orders.reject', $order->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection 