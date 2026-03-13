@extends('layouts.app')

@section('content')
<div class="payment-success-page">
    <div class="success-card">
        <div class="success-header">
            <div class="success-icon">✔️</div>
            <h1>Payment Successful!</h1>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <p class="success-message">
            Thank you for your purchase. Your order has been placed successfully.
        </p>

        <div class="redirect-info">
            You will be redirected to your <a href="{{ route('client.orders.index') }}">Orders Page</a> shortly.
        </div>

        <a href="{{ route('client.orders.index') }}" class="btn-orders">Go to Orders Now</a>
    </div>
</div>

<script>
    // Auto redirect after 3 seconds
    setTimeout(function() {
        window.location.href = "{{ route('client.orders.index') }}";
    }, 3000);
</script>
@endsection
