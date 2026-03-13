<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\PurchaseDeliveredMail;
use Illuminate\Support\Str;


class RiderJobController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $riderID = $user->rider->riderID;

        // Fetch purchases for this rider
        $purchases = DB::table('purchases')
            ->select('PurchaseID', 'CustomerID', 'DeliveryAddress', 'Postcode', 'Status', 'OrderDate')
            ->where('riderID', $riderID)
            ->where('Status', 'Pending')
            ->get()
            ->map(function ($purchase) {

                // Initialize variables
                $address = '';
                $receiver = '';
                $phone = '';

                if ($purchase->DeliveryAddress) {
                    // Split by " | "
                    $parts = explode('|', $purchase->DeliveryAddress);

                    $address = trim($parts[0] ?? '');

                    if (isset($parts[1])) {
                        $receiverPart = trim($parts[1]); // "Receiver: Name (PhoneNumber)"

                        // Extract receiver and phone
                        if (preg_match('/Receiver:\s*(.*?)\s*\((.*?)\)/', $receiverPart, $matches)) {
                            $receiver = $matches[1] ?? '';
                            $phone = $matches[2] ?? '';
                        }
                    }
                }

                $purchase->AddressOnly = $address;
                $purchase->Receiver = $receiver;
                $purchase->PhoneNumber = $phone;

                return $purchase;
            });

        return view('admin.riderJob', compact('purchases', 'riderID'));
    }
    public function markDelivered($id)
    {
        $user = Auth::user();
        $riderID = $user->rider->riderID;

        // 1️⃣ Get purchase + customer + user email
        $purchase = DB::table('purchases')
            ->join('customers', 'purchases.CustomerID', '=', 'customers.CustomerID')
            ->join('users', 'customers.userID', '=', 'users.userID')
            ->select(
                'purchases.*',
                'users.email'
            )
            ->where('purchases.PurchaseID', $id)
            ->where('purchases.riderID', $riderID)
            ->first();

        if (!$purchase) {
            return redirect()->route('admin.rider.jobs')
                ->with('error', 'Purchase not found or not assigned to you.');
        }

        // 2️⃣ Update status
        $updated = DB::table('purchases')
            ->where('PurchaseID', $id)
            ->where('riderID', $riderID)
            ->update([
                'Status' => 'Delivered',
                'DeliveredDate' => now()
            ]);

        if (!$updated) {
            return redirect()->route('admin.rider.jobs')
                ->with('error', 'Failed to update status.');
        }

        // 3️⃣ Send email
        Mail::to($purchase->email)
            ->send(new PurchaseDeliveredMail($purchase));

        // 4️⃣ Redirect
        return redirect()->route('admin.rider.jobs')
            ->with('success', 'Purchase delivered and email sent!');
    }

    public function weatherByCoords(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (!$lat || !$lng) {
            return response()->json(['error' => 'Missing coordinates'], 400);
        }

        $response = Http::get('http://api.weatherstack.com/current', [
            'access_key' => env('WEATHERSTACK_API_KEY'),
            'query' => $lat . ',' . $lng,
            'units' => 'm'
        ]);

        return response()->json($response->json());
    }

    public function weatherByAddress(Request $request)
    {
        $address = $request->query('address');

        if (!$address) {
            return response()->json(['error' => 'Missing address'], 400);
        }

        $response = Http::get('http://api.weatherstack.com/current', [
            'access_key' => env('WEATHERSTACK_API_KEY'),
            'query' => $address,
            'units' => 'm'
        ]);

        return response()->json($response->json());
    }
}
