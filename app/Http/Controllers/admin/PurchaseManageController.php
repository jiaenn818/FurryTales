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
        //check user type
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $query = Purchase::with([
            'customer',
            'items.pet',
            'items.accessory',
            'items.variant',
            'items.outlet'
        ]);

        // 1. Search by PurchaseID or CustomerID
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('PurchaseID', 'LIKE', "%{$search}%")
                  ->orWhere('CustomerID', 'LIKE', "%{$search}%");
            });
        }

        // 2. Filter by Method
        if ($request->filled('method')) {
            $query->where('Method', $request->input('method'));
        }

        // 3. Filter by Status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'Picked Up,Delivered') {
                $query->whereIn('Status', ['Picked Up', 'Delivered']);
            } else {
                $query->where('Status', $status);
            }
        }

        // 4. Date Range
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->input('date_range'));
            if (count($dates) == 2) {
                $query->whereRaw('DATE(OrderDate) >= ?', [$dates[0]])
                      ->whereRaw('DATE(OrderDate) <= ?', [$dates[1]]);
            } elseif (count($dates) == 1) {
                $query->whereRaw('DATE(OrderDate) = ?', [$dates[0]]);
            }
        }

        $purchases = $query->orderBy('OrderDate', 'desc')->paginate(10);
        
        // Append all search queries to pagination links
        $purchases->appends($request->all());

        return view('admin.viewAllPurchase', compact('purchases'));
    }


    public function indexToRiderAssignment()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $riders = Rider::with(['purchases' => function ($query) {
            $query->where('Method', 'Delivery')
                ->where('Status', 'Pending')
                ->orderBy('OrderDate', 'desc');
        }])
            ->withCount(['purchases as order_count' => function ($query) {
                $query->where('Method', 'Delivery')
                    ->where('Status', 'Pending');
            }])
            ->orderBy('postCode', 'asc') 
            ->paginate(10);

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

            $purchase->riderID = $riderID;
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
            ->whereIn('Status', ['Pending', 'Out for Delivery']) // count all active deliveries
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
