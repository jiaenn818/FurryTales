<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Rider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Cookie;

use Carbon\Carbon;

class AuthController extends Controller
{
    // ───────── REGISTER ─────────
    public function register(Request $request)
    {
        $request->validate([
            'custid'      => 'required|string|unique:customers,customerID',
            'name'        => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'phonenumber' => ['required', 'string', 'unique:users,phoneNo', 'regex:/^0\d{9,11}$/'],
            'email'       => 'required|email|unique:users,email',
            'password'    => [
                'required',
                'min:5',
                'max:10',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/'
            ],
            'address'     => 'nullable|string',
            'g-recaptcha-response' => 'required|captcha',
        ] ,[
        'custid.required'      => 'Customer ID is required.',
        'custid.unique'        => 'This Customer ID is already taken.',
        'name.required'        => 'Name is required.',
        'name.regex'           => 'Name can only contain letters and spaces.',
        'email.required'       => 'Email is required.',
        'email.email'          => 'Enter a valid email address.',
        'email.unique'         => 'This email is already registered.',
        'phonenumber.required' => 'Phone number is required.',
        'phonenumber.unique'   => 'This phone number is already registered.',
        'phonenumber.regex'    => 'Enter a valid Malaysian phone number.',
        'password.required'    => 'Password is required.',
        'password.min'         => 'Password must be at least 5 characters.',
        'password.max'         => 'Password must not exceed 10 characters.',
        'password.regex'       => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        'g-recaptcha-response.required' => 'Please complete the CAPTCHA.',
        'g-recaptcha-response.captcha'  => 'CAPTCHA verification failed, please try again.',
    ]);

        // 1. Create User
        $user = new User();
        $user->name      = $request->name;
        $user->phoneNo   = $request->phonenumber;
        $user->email     = $request->email;
        $user->password  = bcrypt($request->password);
        $user->user_type = 'customer';
        $user->save(); // userID auto-generated

        // 2. Create Customer
        $customer = new Customer();
        $customer->customerID = $request->custid; 
        $customer->userID     = $user->userID;
        $customer->address    = $request->address;
        $customer->save();
        
        session([
            'customer_id'   => $customer->customerID,
            'customer_name' => $user->name,
        ]);

        return back()->with('success', 'Registration successful! 🎉');
    }

    // ───────── LOGIN ─────────

    public function login(Request $request)
    {
        $request->validate([
            'userid'   => 'required|string',
            'password' => 'required|string',
        ]);

        // 1. Try to find as Staff
        $staff = Staff::with('user')->where('StaffID', $request->userid)->first();
        $customer = null;
        $user = null;

        if ($staff && $staff->user) {
            $user = $staff->user;
        } else {
            // 2. Try to find as Customer
            $customer = Customer::with('user')->where('customerID', $request->userid)->first();
            if ($customer && $customer->user) {
                $user = $customer->user;
            } else {
                // 3. Try to find as Rider
                $rider = Rider::with('user')->where('riderID', $request->userid)->first();
                if ($rider && $rider->user) {
                    $user = $rider->user;
                }
            }
        }

        if (!$user) {
            // Clear cookie when ID is invalid
            Cookie::queue(Cookie::forget('remember_custid'));
            return back()->with('error', 'Invalid User ID or account not found');
        }

        if($user->status == 'ban') {
            return back()->with('error', 'Your account is banned. Please contact the administrator.');
        }

        // Check if account is locked
        if ($user->lockout_until && Carbon::now()->lessThan($user->lockout_until)) {
            $remainingSeconds = Carbon::now()->diffInSeconds($user->lockout_until);
            $minutes = floor($remainingSeconds / 60);
            $seconds = $remainingSeconds % 60;

            return back()->with('error', "Account temporarily locked. Try again in {$minutes}m {$seconds}s.");
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            // Increment login attempts
            $user->login_attempts = $user->login_attempts + 1;
            $user->save();

            // If "Remember Me" is NOT checked, clear the old cookie
            if (!$request->has('remember')) {
                Cookie::queue(Cookie::forget('remember_custid'));
            }

            // Lock account if 3 failed attempts reached
            if ($user->login_attempts >= 3) {
                $user->update([
                    'lockout_until' => Carbon::now()->addMinutes(2),
                    'login_attempts' => 0, // reset attempts after lock
                ]);

                return back()->with('error', 'Too many failed attempts. Account temporarily locked for 2 minutes.');
            }

            return back()->with('error', 'Incorrect password');
        }

        // Successful login: reset attempts and lockout
        $user->update([
            'login_attempts' => 0,
            'lockout_until' => null,
        ]);

        // Check if "Remember Me" is checked
        $remember = $request->has('remember');

        // Manage "Remember Me" Cookie for User ID
        if ($remember) {
            // Set cookie for 30 days
            Cookie::queue('remember_custid', $request->userid, 43200);
        } else {
            // Clear the cookie completely
            Cookie::queue(Cookie::forget('remember_custid'));
        }

        // Log in user with "Remember Me" option
        Auth::login($user, $remember);

        // Save session data based on type
        if ($staff) {
            session([
                'staff_id'   => $staff->StaffID,
                'user_name'  => $user->name,
                'user_type'  => 'staff',
            ]);
            return redirect()->route('admin.dashboard')->with('success', 'Login successful! Welcome to the Admin Dashboard.');
        } elseif ($user->isRider()) {
            $rider = $user->rider;
            session([
                'rider_id'   => $rider->riderID,
                'user_name'  => $user->name,
                'user_type'  => 'rider',
            ]);
            return redirect()->route('admin.rider.jobs')->with('success', 'Login successful! Welcome back.');
        } else { // Must be a customer if $user is not null and $staff/$rider is null
            session([
                'customer_id'   => $customer ? $customer->customerID : $user->customer->customerID,
                'user_name'     => $user->name,
                'user_type'     => 'customer',
            ]);
            return redirect()->route('client.home')->with('success', 'Login successful!');
        }
    }


    // ───────── SEND OTP ─────────
    public function sendOTP(Request $request)
    {
        $request->validate([
            'customerID' => 'required|string',
        ]);

        $customer = Customer::where('customerID', $request->customerID)->first();
        if (!$customer) {
            return back()->with('error', 'Customer not found');
        }

        $user = $customer->user;
        $otp = rand(100000, 999999);

        session([
            'reset_otp'    => $otp,
            'reset_email'  => $user->email,
            'otp_expire'   => now()->addMinutes(5),
            'otp_verified' => false,
            'customer_id'  => $customer->customerID, 
            'customerID_sent' => $request->customerID, 
        ]);
        

        Mail::send('emails.customer_otp', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('FurryTales Password Reset OTP');
        });

        return back()->with('success', "OTP has sent to {$user->email}! Check your email inbox.");
    }

    // ───────── VERIFY OTP ─────────
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        if (!session()->has('reset_otp') || session('reset_otp') != $request->otp) {
            return back()->with('error', 'Invalid OTP!');
        }

        if (now()->gt(session('otp_expire'))) {
            session()->forget(['reset_otp', 'reset_email', 'otp_expire', 'otp_verified', 'customer_id','customerID_sent']);
            return back()->with('error', 'OTP expired! Please request a new one.');
        }

        session(['otp_verified' => true]);
        return back()->with('success', 'OTP verified! You can reset your password now.');
    }

    // ───────── RESET PASSWORD ─────────
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:5',
                'max:10',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/'
            ]
        ], [
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 5 characters.',
            'password.max' => 'Password must not exceed 10 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if (!session('otp_verified') || !session('customer_id')) {
            return back()->with('error', 'Please verify OTP first!');
        }

        $customer = Customer::where('customerID', session('customer_id'))->first();
        $user = $customer->user;

        $user->password = bcrypt($request->password);
        $user->save();

        session()->forget(['reset_otp', 'reset_email', 'otp_expire', 'otp_verified', 'customer_id','customerID_sent']);

        return view('client.forget', [
            'successMessage' => 'Password reset successful! Redirecting to login...'
        ]);
    }

    // ───────── CLEAR SESSION ─────────
    public function clearSession()
    {
        session()->forget(['reset_otp', 'reset_email', 'otp_expire', 'otp_verified', 'customer_id','customerID_sent']);
        return redirect()->route('client.forget.page');
    }

    // ───────── LOGOUT ─────────
    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('client.login.page')->with('success', 'Logged out successfully!');
    }
}
