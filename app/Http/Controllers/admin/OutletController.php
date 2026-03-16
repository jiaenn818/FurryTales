<?php

namespace App\Http\Controllers\admin;

use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutletController extends Controller
{
    public function index()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $outlets = Outlet::withCount([
            'pets',
            'outletAccessories as accessories_count'
        ])->paginate(5);

        $statesCount = $outlets->pluck('State')->unique()->count();
        $averagePets = $outlets->avg('pets_count') ?? 0;

        $nextOutletID = self::generateNextOutletID();

        return view('admin.outlet', compact(
            'outlets',
            'statesCount',
            'averagePets',
            'nextOutletID'
        ));
    }

    public function edit(Outlet $outlet)
    {
        $outlet->load([
            'pets',
            'outletAccessories.variant.accessory'
        ]);

        return response()->json($outlet);
    }

    public function store(Request $request)
    {
        $request->validate([
            'OutletID' => 'required|string|unique:outlet,OutletID|max:10',
            'AddressLine1' => 'required|string|max:255',
            'City' => 'required|string|max:100',
            'State' => 'required|string|max:50',
            'PostCode' => 'required|string|max:5',
        ]);

        Outlet::create($request->only([
            'OutletID',
            'AddressLine1',
            'City',
            'State',
            'PostCode'
        ]));

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet created successfully!');
    }

    public function update(Request $request, Outlet $outlet)
    {
        $request->validate([
            'AddressLine1' => 'required|string|max:255',
            'City' => 'required|string|max:100',
            'State' => 'required|string|max:50',
            'PostCode' => 'required|string|max:5',
        ]);

        $outlet->update($request->only([
            'AddressLine1',
            'City',
            'State',
            'PostCode'
        ]));

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Outlet updated successfully!');
    }

    public function destroy(Outlet $outlet)
    {
        if ($outlet->pets()->count() > 0) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot delete outlet with associated pets!'
            ], 400);
        }

        $outlet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Outlet deleted successfully!'
        ]);
    }

    public static function generateNextOutletID()
    {
        $latest = Outlet::orderBy('OutletID', 'desc')->first();

        $number = $latest
            ? ((int) preg_replace('/\D/', '', $latest->OutletID) + 1)
            : 1;

        return 'OUT' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
