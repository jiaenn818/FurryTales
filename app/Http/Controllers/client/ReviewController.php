<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\OrderRating;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Show all orders and reviews
    public function index()
    {
        $user = Auth::user();

        if (!$user || !$user->customer) {
            return redirect()->route('client.login.page')->with('error', 'Please login to view reviews.');
        }

        $customerId = $user->customer->customerID;

        // Get all orders for this customer, with existing review
        $orders = Purchase::with('orderRating')
            ->where('CustomerID', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all reviews for display (with eager loading to prevent crashes and N+1)
        $reviews = OrderRating::with(['purchase.customer.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.reviews', compact('orders', 'reviews'));
    }

    // Store review submission
    public function store(Request $request)
    {
        $request->validate([
            'PurchaseID' => 'required|exists:purchases,PurchaseID',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $customerId = $user->customer->customerID;

        $purchaseID = $request->PurchaseID;

        // Find order
        $order = Purchase::where('PurchaseID', $purchaseID)
                         ->where('CustomerID', $customerId)
                         ->firstOrFail();

        // Check order status
        if (!in_array($order->Status, ['Picked Up', 'Delivered'])) {
            return redirect()->back()->with('error', 'You can only review orders that have been picked up or delivered.');
        }

        // Prevent duplicate reviews
        $existingReview = OrderRating::where('PurchaseID', $purchaseID)->first();
        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this order.');
        }

        // Create review
        OrderRating::create([
            'PurchaseID' => $purchaseID,
            'CustomerID' => $user->customer->customerID,
            'rating'     => $request->rating,   // <-- must be included
            'review'     => $request->review
        ]);

        return redirect()->route('client.reviews.index')->with('success', 'Your review has been submitted!');
    }
}
