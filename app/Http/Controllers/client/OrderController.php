<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;


class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login to view orders.');
        }

        $user = Auth::user();
        if (!$user->customer) {
            return redirect()->route('client.home')->with('error', 'Customer profile missing.');
        }
        $customerId = $user->customer->customerID;

        // Start query
        $ordersQuery = Purchase::where('CustomerID', $customerId);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $ordersQuery->where('Status', $request->status);
        }

        // Filter by date
        if ($request->has('date') && $request->date != '') {
            $ordersQuery->whereDate('created_at', $request->date);
        }

        // Determine sort column and order
        $sortColumn = $request->get('sort_by', 'created_at'); // 'created_at' or 'TotalAmount'
        $sortOrder = $request->get('sort_order', 'desc'); // 'asc' or 'desc'

        // Validate sort column
        if (!in_array($sortColumn, ['created_at', 'TotalAmount'])) {
            $sortColumn = 'created_at';
        }

        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $ordersQuery->orderBy($sortColumn, $sortOrder);

        $orders = $ordersQuery->paginate(5);

        return view('client.orders.index', compact('orders'));
    }


    public function show($id)
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login first.');
        }

        $user = Auth::user();
        if (!$user->customer) {
            return redirect()->route('client.home')->with('error', 'Customer profile missing.');
        }
        $customerId = $user->customer->customerID;

        $order = Purchase::with(['items.pet', 'items.accessory', 'items.variant', 'items.outlet', 'payment'])
                         ->where('PurchaseID', $id)
                         ->where('CustomerID', $customerId)
                         ->firstOrFail();

        return view('client.orders.show', compact('order'));
    }

    public function downloadReceipt($id)
    {
        // Load both customer and items relationships
        $order = Purchase::with(['items', 'customer'])->findOrFail($id);
        
        $pdf = Pdf::loadView('client.orders.receipt-pdf', compact('order'));
        
        return $pdf->download('FurryTalesReceipt-' . $order->PurchaseID . '.pdf');
    }

    public function printReceipt($id)
    {
        // Load both customer and items relationships
        $order = Purchase::with(['items', 'customer'])->findOrFail($id);
        
        return view('client.orders.receipt-pdf', compact('order'));
    }

}
