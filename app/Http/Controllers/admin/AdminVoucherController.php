<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class AdminVoucherController extends Controller
{
    public function index()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $vouchers = Voucher::orderBy('createdAt', 'desc')->paginate(10);

        return view('admin.viewAllVoucher', compact('vouchers'));
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'voucherID'      => 'required|string|max:50|unique:voucher,voucherID',
            'discountAmount' => 'required|numeric|min:0',
            'minSpend'       => 'required|numeric|min:0',
            'expiryDate'     => 'nullable|date',
            'usageLimit'     => 'nullable|integer|min:1',
        ]);

        // Create voucher
        Voucher::create([
            'voucherID'      => $request->voucherID,
            'discountAmount' => $request->discountAmount,
            'minSpend'       => $request->minSpend,
            'expiryDate'     => $request->expiryDate,
            'usageLimit'     => $request->usageLimit, // null = unlimited
        ]);

        return redirect()->back()->with('success', 'Voucher created successfully!');
    }

    public function update(Request $request, $id)
    {
        // Find voucher by primary key
        $voucher = Voucher::findOrFail($id);

        // Validate input
        $request->validate([
            'discountAmount' => 'required|numeric|min:0',
            'minSpend'       => 'required|numeric|min:0',
            'expiryDate'     => 'nullable|date',
            'usageLimit'     => 'nullable|integer|min:1',
        ]);

        // Update fields
        $voucher->update([
            'discountAmount' => $request->discountAmount,
            'minSpend'       => $request->minSpend,
            'expiryDate'     => $request->expiryDate,
            'usageLimit'     => $request->usageLimit, // null = unlimited
        ]);

        return redirect()->back()->with('success', 'Voucher updated successfully!');
    }

    public function destroy($id)
    {
        // Find voucher by primary key
        $voucher = Voucher::findOrFail($id);

        // Delete voucher
        $voucher->delete();

        return redirect()->back()->with('success', 'Voucher deleted successfully!');
    }
}
