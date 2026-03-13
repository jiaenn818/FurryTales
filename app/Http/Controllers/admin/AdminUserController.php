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

    public function index()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $currentUser = Auth::user();
        $isManager = $currentUser->isManager();  

        $query = User::with(['customer', 'staff.outlet', 'rider']);

        if (!$isManager) {
            $query->whereHas('customer');
        }

        $users = $query->paginate(10);

        $outlets = Outlet::all();
        $roles = Staff::getRoles();

        return view('admin.viewAllUsers', compact('users', 'outlets', 'roles', 'isManager'));
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
