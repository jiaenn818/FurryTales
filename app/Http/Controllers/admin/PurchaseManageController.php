<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Rider;
use Illuminate\Support\Facades\Auth;

class PurchaseManageController extends Controller
{

    public function indexToPurchaseManage(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $query = Purchase::with([
            'customer',
            'items.pet',
            'items.accessory',
            'items.variant',
            'items.outlet'
        ])->orderBy('OrderDate', 'desc');

        // Search by PurchaseID or CustomerID
        if ($request->filled('search')) {
            $term = strtolower($request->search);
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(PurchaseID) like ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(CustomerID) like ?', ["%{$term}%"]);
            });
        }

        // Filter by date range
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if(!empty($dates[0])) $query->whereDate('OrderDate', '>=', $dates[0]);
            if(!empty($dates[1])) $query->whereDate('OrderDate', '<=', $dates[1]);
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('Method', $request->method);  
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'Picked Up,Delivered') {
                $query->whereIn('Status', ['Picked Up', 'Delivered']);
            } else {
                $query->where('Status', $request->status);
            }
        }

        // Paginate 10 per page
        $purchases = $query->paginate(10)->withQueryString(); // preserve query string for links

        return view('admin.viewAllPurchase', compact('purchases'));
    }
    public function indexToRiderAssignment()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $riders = Rider::with(['purchases' => function ($query) {
            $query->where('Method', 'Delivery')
                ->where('Status', 'Out for Delivery')
                ->orderBy('OrderDate', 'desc');
        }])
            ->withCount(['purchases as order_count' => function ($query) {
                $query->where('Method', 'Delivery')
                    ->where('Status', 'Out for Delivery');
            }])
            ->orderBy('postCode', 'asc')
            ->get();

        // Get all unassigned deliveries
        $allUnassigned = Purchase::where('Method', 'Delivery')
            ->whereNull('riderID')
            ->orderBy('OrderDate', 'desc')
            ->get();

        // Map unassigned purchases for each rider sorted by postcode similarity
        $riders->map(function ($rider) use ($allUnassigned) {
            $rider->unassignedPurchases = $allUnassigned->sortByDesc(function ($purchase) use ($rider) {
                $riderPostcode = (string)$rider->postCode;
                $purchasePostcode = (string)($purchase->Postcode ?? '');
                $matchCount = 0;

                for ($i = 0; $i < min(strlen($riderPostcode), strlen($purchasePostcode)); $i++) {
                    if ($riderPostcode[$i] === $purchasePostcode[$i]) {
                        $matchCount++;
                    } else {
                        break;
                    }
                }

                return $matchCount; // Sort by highest match first
            })->values(); // reset keys

            return $rider;
        });

        return view('admin.riderAssignment', compact('riders'));
    }

    public function assignPurchase(Request $request, $riderID)
    {
        $request->validate([
            'purchaseID' => 'required|exists:purchases,PurchaseID',
        ]);

        $purchase = Purchase::findOrFail($request->purchaseID);

        // Only assign if Delivery and currently unassigned
        if ($purchase->Method === 'Delivery' && is_null($purchase->riderID)) {

            // Check if rider exceeded limit
            if ($this->checkPurchaseLimit($riderID)) {
                return redirect()->back()->with('error', "Rider {$riderID} has reached the maximum allowed active deliveries (5).");
            }

            // Assign rider
            $purchase->riderID = $riderID;

            // If status is Pending, update to Out for Delivery
            if ($purchase->Status === 'Pending') {
                $purchase->Status = 'Out for Delivery';
            }

            $purchase->save();

            return redirect()->back()->with('success', "Purchase {$purchase->PurchaseID} assigned to rider {$riderID}");
        }

        return redirect()->back()->with('error', "Purchase cannot be assigned.");
    }

    public function checkPurchaseLimit($riderID)
    {
        // Count only active deliveries
        $activeDeliveries = Purchase::where('riderID', $riderID)
            ->where('Method', 'Delivery')
            ->whereIn('Status', ['Out for Delivery'])
            ->count();

        return $activeDeliveries >= 5; // true if limit reached
    }
    public function updateStatus(Request $request, $purchaseID)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Purchase::getStatusEnum())
        ]);

        $purchase = Purchase::findOrFail($purchaseID);
        $purchase->Status = $request->status;
        $purchase->save();

        return redirect()->route('admin.purchases')->with('success', 'Purchase status updated successfully!');
    }
}
