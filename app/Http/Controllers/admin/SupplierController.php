<?php

namespace App\Http\Controllers\admin;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    // Display all suppliers
    public function index()
    {
        //check user type
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $suppliers = Supplier::with([
            'pets',
            'accessories.variants.outlets'
        ])->get();
        $totalSuppliers = $suppliers->count();
        $totalPets = $suppliers->sum('pets_count');
        $averagePets = $totalSuppliers > 0 ? $totalPets / $totalSuppliers : 0;

        $nextSupplierID = Supplier::generateNextSupplierID();

        return view('admin.supplier', compact(
            'suppliers',
            'totalSuppliers',
            'totalPets',
            'averagePets',
            'nextSupplierID'
        ));
    }

    // Store new supplier
    public function store(Request $request)
    {
        $request->validate([
            'SupplierID' => 'required|unique:supplier,SupplierID|regex:/^SUP\d{3}$/',
            'SupplierName' => 'required|string|max:255',
            'SupplierEmail' => 'required|email|max:255',
            'SupplierPhoneNumber' => 'required|string|max:20',
            'SupplierAddress' => 'required|string|max:255', // <-- add validation
        ]);
        
        Supplier::create($request->only([
            'SupplierID',
            'SupplierName',
            'SupplierEmail',
            'SupplierPhoneNumber',
            'SupplierAddress', // <-- add here
        ]));

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier added successfully.');
    }

    // Update supplier
    public function update(Request $request, $id)
    {
        $supplier = Supplier::where('SupplierID', $id)->firstOrFail();

        $request->validate([
            'SupplierID' => 'required|unique:supplier,SupplierID|regex:/^SUP\d{3}$/',
            'SupplierName' => 'required|string|max:255',
            'SupplierEmail' => 'required|email|max:255',
            'SupplierPhoneNumber' => 'required|string|max:20',
            'SupplierAddress' => 'required|string|max:255', // <-- add validation
        ]);
        
        Supplier::create($request->only([
            'SupplierID',
            'SupplierName',
            'SupplierEmail',
            'SupplierPhoneNumber',
            'SupplierAddress', // <-- add here
        ]));

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    // Delete supplier
    public function destroy($id)
    {
        $supplier = Supplier::where('SupplierID', $id)->firstOrFail();
        $supplier->delete();

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
