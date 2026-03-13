<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $order->PurchaseID }}</title>
    <style>
    {!! file_get_contents(public_path('css/receipt-pdf.css')) !!}
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">Furry Tales</div>
            <div class="company-info">
                38-A, Jalan SS 22/25, Damansara Jaya, 47400 Petaling Jaya, Selangor<br>
                Email: furrytales@gmail.com | Phone: +03 4904255
            </div>
        </div>

        <!-- Receipt Header with Details -->
        <div class="receipt-header">
            <div class="receipt-title">Receipt</div>
            <div class="receipt-details">
                <div class="detail-item">
                    <div class="detail-label">Order ID</div>
                    <div class="detail-value">#{{ $order->PurchaseID }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date</div>
                    <div class="detail-value">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Customer</div>
                    <div class="detail-value">{{ $order->customer->user->name ?? 'Guest' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Transaction ID</div>
                    <div class="detail-value">{{ $order->payment->TransactionID ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="items-section">
            <h3 class="section-title">Items Purchased</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Unit Price (MYR)</th>
                        <th class="text-right">Total Price (MYR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    @php
                        $isPet = !empty($item->ItemID);
                        $product = $isPet ? $item->pet : $item->accessory;
                        $name = $isPet ? ($product->PetName ?? 'Unknown Pet') : ($product->AccessoryName ?? 'Unknown Accessory');
                        $unitPrice = $item->Price; // Use snapshot price directly
                        $totalPrice = $unitPrice * $item->Quantity;
                    @endphp
                    <tr>
                        <td>
                            <div class="item-name">{{ $name }}</div>
                            @if($isPet && isset($product->Breed))
                                <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $product->Breed }}</div>
                            @elseif(!$isPet && isset($product->Brand))
                                <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $product->Brand }}</div>
                            @endif
                        </td>
                        <td class="text-right">{{ $item->Quantity }}</td>
                        <td class="text-right">{{ number_format($unitPrice, 2) }}</td>
                        <td class="text-right">{{ number_format($totalPrice, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <!-- Total Section -->
        <div class="total-section">
            @php
                $calculatedSubtotal = $order->items->sum(function($item) {
                    $unitPrice = $item->Price;
                    return $unitPrice * $item->Quantity;
                });
            @endphp

            <div class="total-row" style="display: flex; justify-content: space-between; gap: 20px;">
                <div class="total-label">Subtotal</div>
                <div class="total-value">MYR {{ number_format($calculatedSubtotal, 2) }}</div>
            </div>

            @if($order->VoucherID)
            <div class="total-row" style="display: flex; justify-content: space-between; gap: 20px;">
                <div class="total-label">Voucher Applied ({{ $order->VoucherID }})</div>
                <div class="total-value" style="color: #e11d48;">-MYR {{ number_format($order->DiscountAmount, 2) }}</div>
            </div>
            @endif

            <div class="total-row" style="margin-top: 10px; border-top: 2px solid #333; padding-top: 10px; display: flex; justify-content: space-between; gap: 20px;">
                <div class="total-label" style="font-size: 18px; font-weight: bold;">Grand Total</div>
                <div class="total-value" style="font-size: 18px; font-weight: bold; color: #5a2c2c;">MYR {{ number_format($order->TotalAmount, 2) }}</div>
            </div>
        </div>

        <!-- Payment & Delivery Info Grid -->
        <div class="info-grid">
            <!-- Payment Details -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-icon">💳</div>
                    <div class="info-card-title">Payment Details</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">{{ $order->payment->PaymentMethod ?? 'Stripe Secure Payment' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Transaction ID</div>
                    <div class="info-code">{{ $order->payment->TransactionID ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date Paid</div>
                    <div class="info-value">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>

            <!-- Delivery Details -->
            <div class="info-card">
                <div class="info-card-header">
                    <div class="info-icon">🚚</div>
                    <div class="info-card-title">Delivery Information</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Method</div>
                    <div class="info-value">{{ ucfirst($order->Method) }}</div>
                </div>
                @if($order->DeliveryAddress)
                <div class="info-item">
                    <div class="info-label">Address</div>
                    <div class="info-value">{{ $order->DeliveryAddress }}</div>
                </div>
                @endif
                @if($order->Postcode)
                <div class="info-item">
                    <div class="info-label">Postcode</div>
                    <div class="info-value">{{ $order->Postcode }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">Thank you for your purchase! 🐾</div>
            <div class="footer-note">This is an electronically generated receipt.</div>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>