@extends('layouts.app')

@section('content')
<div class="orders-show-page">
    <div class="container">
        <div class="orders-show-container">
        
        <!-- Back Button -->
        <a href="{{ route('client.orders.index') }}" class="orders-back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Orders
        </a>
        <div class="receipt-actions" style="margin: 20px 0; display: flex; gap: 10px;">
            <button onclick="window.open('{{ route('client.order.print-receipt', $order->PurchaseID) }}', '_blank')" class="btn btn-primary">
                🖨️ Print Receipt
            </button>
                        
            <a href="{{ route('client.order.download-receipt', $order->PurchaseID) }}" class="btn btn-success">
                📥 Download Receipt
            </a>
</div>


        <div class="orders-show-card">
            <!-- Header -->
            <div class="orders-show-header">
                <div class="orders-show-header-content">
                    <div>
                        <h1>Order #{{ $order->PurchaseID }}</h1>
                        <p>Placed on {{ $order->created_at->format('d F Y, h:i A') }}</p>
                    </div>
                    <div class="orders-show-header-right">
                        @php
                            $statusClass = 'order-status-default';
                            if ($order->Status === 'Pending') $statusClass = 'order-status-pending';
                            elseif ($order->Status === 'Picked Up' || $order->Status === 'Delivered') $statusClass = 'order-status-delivered';
                            elseif ($order->Status === 'Out for Delivery') $statusClass = 'order-status-out-for-delivery';
                        @endphp
                        <span class="order-status {{ $statusClass }}">
                            {{ $order->Status }}
                        </span>
                        <p class="orders-show-total">MYR {{ number_format($order->TotalAmount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="orders-show-content">
                
                <!-- Items List -->
                <div class="orders-show-section">
                    <h3>Items Purchased</h3>
                    <div class="order-items-list">
                        @foreach($order->items as $item)
                        @php
                            $isPet = !empty($item->ItemID);
                            $product = $isPet ? $item->pet : $item->accessory;
                            $name = $isPet ? ($product->PetName ?? 'Unknown Pet') : ($product->AccessoryName ?? 'Unknown Accessory');
                            $image = $isPet ? ($product->ImageURL1 ?? null) : ($product->ImageURL1 ?? null);
                            $subText = $isPet ? ($product->Breed ?? '') : ($product->Brand ?? '');
                        @endphp
                        <div class="order-item-card">
                            <div class="order-item-content">
                                <div class="order-item-image">
                                     <!-- Item Image -->
                                     @if($image)
                                        <img src="{{ asset($image) }}" alt="{{ $name }}">
                                     @else
                                        <div class="order-item-image-placeholder">
                                            {{ $isPet ? '🐾' : '📦' }}
                                        </div>
                                     @endif
                                </div>
                                <div class="order-item-info">
                                    <h4>{{ $name }}</h4>
                                    <div class="order-item-badges">
                                        @if($subText)
                                            <span>{{ $subText }}</span>
                                        @endif
                                        <span style="margin-left: 2rem;"><strong>Quantity : </strong> {{ $item->Quantity }}</span>
                                    </div>
                                    @if(!$isPet)
                                        <div class="order-item-selections" style="margin-top: 0.5rem; font-size: 0.85rem; color: #4b5563;">
                                            {{-- Display Variant/Selected Details --}}
                                            @if($item->variant)
                                                <div style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px; display: inline-block; margin-bottom: 4px; font-weight: 600;">
                                                    {{ collect(explode('|', $item->variant->VariantKey))
                                                        ->map(fn($part) => trim(explode(':', $part)[1] ?? $part))
                                                        ->implode(', ') }}
                                                </div>
                                            @endif

                                            {{-- Display Selected Outlet --}}
                                            @if($item->OutletID)
                                                <div class="item-outlet" style="margin-top: 0.25rem; font-size: 0.8rem; color: #f7941d; font-weight: 600;">
                                                    <span>📍 Store: {{ $item->outlet->City }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="order-item-price">
                                <p class="order-item-price-label">Price</p>
                                @if(!$isPet && $item->Quantity > 1)
                                    <p class="order-item-price-value" style="font-size: 0.85rem; color: #6b7280; margin-bottom: 0.25rem;">
                                        MYR {{ number_format($item->Price, 2) }} × {{ $item->Quantity }}
                                    </p>
                                    <p class="order-item-price-value">MYR {{ number_format($item->Price * $item->Quantity, 2) }}</p>
                                @else
                                    <p class="order-item-price-value">MYR {{ number_format($item->Price * $item->Quantity, 2) }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary / Detailed Pricing -->
                <div class="orders-show-section" style="margin-top: 2rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                    <div style="max-width: 400px; margin-left: auto;">
                        @php
                            $calculatedSubtotal = $order->items->sum(function($item) {
                                return $item->Price * $item->Quantity;
                            });
                        @endphp
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; gap: 1rem;">
                            <span style="color: #6b7280;">Subtotal</span>
                            <span style="font-weight: 600;">MYR {{ number_format($calculatedSubtotal, 2) }}</span>
                        </div>

                        @if($order->VoucherID)
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; gap: 1rem;">
                            <span style="color: #6b7280;">Voucher Applied ({{ $order->VoucherID }})</span>
                            <span style="color: #e11d48; font-weight: 700;">-MYR {{ number_format($order->DiscountAmount, 2) }}</span>
                        </div>
                        @endif

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #e5e7eb; gap: 1rem;">
                            <span style="font-size: 1.1rem; font-weight: 700; color: #111827;">Total Paid</span>
                            <span style="font-size: 1.5rem; font-weight: 800; color: #5a2c2c;">MYR {{ number_format($order->TotalAmount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping & Details Grid -->
                <div class="orders-show-details-grid">
                    <!-- Delivery Info -->
                    <div class="orders-show-detail-card">
                        <div class="orders-show-detail-header">
                            <div class="orders-show-detail-icon orders-show-detail-icon-delivery">
                                🚚
                            </div>
                            <h3>Delivery Information</h3>
                        </div>
                        
                        <div class="orders-show-detail-content">
                            <div class="orders-show-detail-item">
                                <p class="orders-show-detail-label">Method</p>
                                <p class="orders-show-detail-value">{{ ucfirst($order->Method) }}</p>
                            </div>
                            @if($order->DeliveryAddress)
                            <div class="orders-show-detail-item">
                                <p class="orders-show-detail-label">Address</p>
                                <p class="orders-show-detail-text">{{ $order->DeliveryAddress }}</p>
                            </div>
                            @endif
                            @if($order->Postcode)
                            <div class="orders-show-detail-item">
                                <p class="orders-show-detail-label">Postcode</p>
                                <p class="orders-show-detail-text">{{ $order->Postcode }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Payment Info -->
                    <div class="orders-show-detail-card">
                        <div class="orders-show-detail-header">
                            <div class="orders-show-detail-icon orders-show-detail-icon-payment">
                                💳
                            </div>
                            <h3>Payment Details</h3>
                        </div>

                        <div class="orders-show-detail-content">
                            <div class="orders-show-detail-item">
                                <p class="orders-show-detail-label">Payment Method</p>
                                <p class="orders-show-detail-value">{{ $order->payment->PaymentMethod ?? 'Stripe Secure Payment' }}</p>
                            </div>
                            <div class="orders-show-detail-item">
                                <p class="orders-show-detail-label">Transaction ID</p>
                                <p class="orders-show-detail-code">
                                    {{ $order->payment->TransactionID ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="orders-show-detail-item">
                                <p class="orders-show-detail-label">Date Paid</p>
                                <p class="orders-show-detail-text">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        </div>
    </div>
</div>
@endsection
