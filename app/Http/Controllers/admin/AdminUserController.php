<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Rider;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;


class AdminUserController extends Controller
{

    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $currentUser = Auth::user();
        $isManager = $currentUser->isManager();

        $query = User::with(['customer', 'staff.outlet', 'rider']);

        // Filter by type tab
        $type = $request->get('type', 'all'); // default to 'all'

        if (!$isManager) {
            // Non-managers: for staff/rider/all tabs, return empty query
            if ($type === 'staff' || $type === 'rider' || $type === 'all') {
                $query->whereRaw('0 = 1'); // always false, returns no rows
            }
            // customer tab can still show customers if you want, or you can also block
        } else {
            // Managers: normal filtering
            if ($type === 'customer') {
                $query->whereHas('customer');
            } elseif ($type === 'staff') {
                $query->whereHas('staff');
            } elseif ($type === 'rider') {
                $query->whereHas('rider');
            }
            // 'all' shows everything
        }

        // Server-side search (simple)
        if ($request->filled('search')) {
            $search = strtolower($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(userID) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(phoneNo) LIKE ?', ["%{$search}%"]);
            });
        }

        $users = $query->paginate(10)->withQueryString(); // keep search & type params in pagination

        $outlets = Outlet::all();
        $roles = Staff::getRoles();

        return view('admin.viewAllUsers', compact('users', 'outlets', 'roles', 'isManager', 'type'));
    }
    public function updateStatus(Request $request, $userID)
    {
        $request->validate([
            'status' => 'required|in:active,ban',
        ]);

        $user = User::findOrFail($userID);
        $user->status = $request->status;
        $user->save();

        return redirect()->back()->with('success', 'User status updated!');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phoneNo' => 'required|unique:users,phoneNo',
            'password' => 'required|min:6',
            'type' => 'required|in:staff,rider',

            'StaffID' => $request->type === 'staff'
                ? 'required|unique:staff,StaffID'
                : 'nullable',

            'riderID' => $request->type === 'rider'
                ? 'required|unique:rider,riderID'
                : 'nullable',
        ]);

        $otp = rand(100000, 999999);

        session([
            'pending_user' => $request->all(),
            'otp' => $otp,
            'otp_expiry' => now()->addMinutes(5)
        ]);

        Mail::to($request->email)->send(new OtpMail($otp));

        return redirect()->route('admin.otp.page');
    }

    public function verifyOtp(Request $request)
    {
        if ($request->otp != session('otp')) {
            return back()->withErrors(['otp' => 'Invalid OTP']);
        }

        if (now()->greaterThan(session('otp_expiry'))) {
            return back()->withErrors(['otp' => 'OTP expired']);
        }

        $pending = session('pending_user');

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'phoneNo' => $pending['phoneNo'],
            'password' => bcrypt($pending['password']),
            'status' => 'active',
            'user_type' => $pending['type'],
        ]);

        if ($pending['type'] === 'staff') {
            Staff::create([
                'StaffID' => $pending['StaffID'],
                'UserID' => $user->userID,
                'OutletID' => $pending['OutletID'],
                'Role' => $pending['Role'],
            ]);
        }

        if ($pending['type'] === 'rider') {
            Rider::create([
                'riderID' => $pending['riderID'],
                'userID' => $user->userID,
                'postCode' => $pending['postCode'],
            ]);
        }

        session()->forget(['pending_user', 'otp', 'otp_expiry']);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully!');
    }

    public function resendOtp()
    {
        $pending = session('pending_user');

        if (!$pending) {
            return redirect()->route('admin.users')
                ->withErrors(['otp' => 'No pending OTP request found.']);
        }

        $otp = rand(100000, 999999);

        session([
            'otp' => $otp,
            'otp_expiry' => now()->addMinutes(5)
        ]);

        Mail::to($pending['email'])->send(new OtpMail($otp));

        return back()->with('success', 'OTP resent successfully!');
    }

    public function destroy($userID)
    {
        $user = User::with(['customer', 'staff', 'rider'])->findOrFail($userID);

        // Delete related child first (to avoid FK constraint errors)
        if ($user->customer) {
            $user->customer->delete();
        }
        if ($user->staff) {
            $user->staff->delete();
        }
        if ($user->rider) {
            $user->rider->delete();
        }

        // Then delete the user
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}
