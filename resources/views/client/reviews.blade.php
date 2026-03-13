@extends('layouts.app')

@section('content')
<div class="reviews-page container">
    <h1>Order Reviews</h1>

    <!-- Show success / error -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h2>Your Orders</h2>

    @forelse($orders as $order)
        <div class="order-review-card">
            <h3>Order #{{ $order->PurchaseID }} ({{ $order->Status }})</h3>

            @php
                $reviewed = $order->orderRating ?? null;
            @endphp

            @if($reviewed)
                <div class="review-details">
                    <div class="reviewer-info">
                        @php
                            $customer = $reviewed->customer ?? ($reviewed->purchase->customer ?? null);
                            $profilePhoto = $customer->profile_photo ?? 'profile.png';
                            $reviewerName = $customer->user->name ?? 'Anonymous';
                        @endphp
                        <img src="{{ asset('image/' . $profilePhoto) }}" class="reviewer-photo">
                        <span class="reviewer-name">{{ $reviewerName }}</span>
                        <span class="review-date">{{ $reviewed->created_at->format('d M Y') }}</span>
                    </div>
                    <p>⭐ Rating: {{ $reviewed->rating }}/5</p>
                    <p>{{ $reviewed->review }}</p>
                </div>
            @elseif(in_array($order->Status, ['Picked Up', 'Delivered']))
                <form action="{{ route('client.reviews.store') }}" method="POST" class="review-form">
                    @csrf
                    <input type="hidden" name="PurchaseID" value="{{ $order->PurchaseID }}">
                    <label>Rating:</label>
                    <select name="rating" required>
                        <option value="">Select</option>
                        @for($i=1;$i<=5;$i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <label>Review:</label>
                    <textarea name="review" placeholder="Optional review..."></textarea>
                    <button type="submit">Submit Review</button>
                </form>
            @else
                <p class="cannot-review-text">You cannot review this order yet.</p>
            @endif
        </div>
    @empty
        <div class="orders-empty">
            <div class="orders-empty-icon">📦</div>
            <h3>No orders found</h3>
            <p>Looks like you haven't made any purchases yet.</p>
            <a href="{{ route('client.pets.index') }}" class="orders-empty-button">
                Browse Pets
            </a>
        </div>
    @endforelse


    <h2>All Reviews</h2>

    @forelse($reviews as $review)
        <div class="review-card">
            <div class="reviewer-info">
                @php
                    $customer = $review->purchase->customer ?? ($review->purchase->customer ?? null);
                    $profilePhoto = $customer->profile_photo ?? 'profile.png';
                    $reviewerName = $customer->user->name ?? 'Anonymous';
                @endphp
                <img src="{{ asset('image/' . $profilePhoto) }}" class="reviewer-photo">
                <span class="reviewer-name">{{ $reviewerName }}</span>
                <span class="review-date">{{ $review->created_at->format('d M Y') }}</span>
            </div>
            <h4>Order #{{ $review->PurchaseID }}</h4>
            <p>⭐ {{ $review->rating }}/5</p>
            <p>{{ $review->review }}</p>
        </div>
    @empty
        <div class="orders-empty">
            <div class="orders-empty-icon">⭐</div>
            <h3>No reviews found</h3>
            <p>No reviews have been submitted yet.</p>
        </div>
    @endforelse

</div>
@endsection
