@extends('layouts.app')

@section('content')
@vite(['resources/css/app.css'])
<div class="orders-page">
    <div class="container">
        <div class="orders-container">
            <div class="orders-header">
                <h1>My Orders</h1>
                <p>Track and manage your purchase history.</p>
            </div>

          <div class="orders-filter-sort">
    <form method="GET" action="{{ route('client.orders.index') }}" class="filter-form">
        <div class="filter-group">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status">
                <option value="">All</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Picked Up" {{ request('status') == 'Picked Up' ? 'selected' : '' }}>Picked Up</option>
                <option value="Out for Delivery" {{ request('status') == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="date">Filter by Date:</label>
            <input type="date" name="date" id="date" value="{{ request('date') }}">
        </div>

        <div class="filter-group">
            <label for="sort_by">Sort By:</label>
            <select name="sort_by" id="sort_by">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                <option value="TotalAmount" {{ request('sort_by') == 'TotalAmount' ? 'selected' : '' }}>Total Amount</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="sort_order">Order:</label>
            <select name="sort_order" id="sort_order">
                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
            </select>
        </div>

        <button type="submit" class="filter-submit">Apply</button>
    </form>
</div>


            @if($orders->isEmpty())
                <div class="orders-empty">
                    <div class="orders-empty-icon">📦</div>
                    <h3>No orders found</h3>
                    <p>Looks like you haven't made any purchases yet.</p>
                    <a href="{{ route('client.pets.index') }}" class="orders-empty-button">
                        Browse Pets
                    </a>
                </div>
            @else
                <div class="orders-list">
                    @foreach($orders as $order)
                        <div class="order-card">
                            
                            <!-- Background Decoration -->
                            <div class="order-card-decoration"></div>

                            <div class="order-card-content">
                                
                                <!-- Order Info -->
                                <div class="order-info">
                                    <div class="order-header">
                                        <h3>Order #{{ $order->PurchaseID }}</h3>
                                        @php
                                            $statusClass = 'order-status-default';
                                            if ($order->Status === 'Pending') $statusClass = 'order-status-pending';
                                            elseif ($order->Status === 'Picked Up' || $order->Status === 'Delivered') $statusClass = 'order-status-delivered';
                                            elseif ($order->Status === 'Out for Delivery') $statusClass = 'order-status-out-for-delivery';
                                        @endphp
                                        <span class="order-status {{ $statusClass }}">
                                            {{ $order->Status }}
                                        </span>
                                    </div>
                                    <div class="order-meta">
                                        <div class="order-meta-item">
                                            <span class="icon">📅</span>
                                            {{ $order->created_at->format('d M Y, h:i A') }}
                                        </div>
                                        <span class="divider"></span>
                                        <div class="order-meta-item">
                                            <span class="icon">💳</span>
                                            MYR <span class="amount">{{ number_format($order->TotalAmount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="order-actions">
                                    <a href="{{ route('client.orders.show', $order->PurchaseID) }}" class="order-view-button">
                                        View Details
                                        <span>→</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="orders-pagination" style="margin-top: 2rem; display: flex; justify-content: center;">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
