<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Display profile
    public function show()
    {
        if (!Auth::check()) {
             return redirect()->route('client.login.page')->with('error', 'Please login first');
        }

        $user = Auth::user();
        if (!$user->customer) {
            return view('Client.profile.view', ['customerData' => null]);
        }
        
        // Reload relationships to ensure freshness
        $customerData = Customer::with('user')->where('CustomerID', $user->customer->customerID)->first();

        return view('Client.profile.view', ['customerData' => $customerData]);
    }

    // Edit profile
    public function edit()
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login first');
        }
        
        $user = Auth::user();
        $customerData = Customer::with('user')->where('CustomerID', $user->customer->customerID)->first();
        
        return view('Client.profile.edit', ['customerData' => $customerData]);
    }

    // Update profile
    public function update(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login first');
        }
        $customerData = Customer::with('user')->where('CustomerID', Auth::user()->customer->customerID)->first();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255|unique:users,email,' . Auth::user()->userID . ',userID',
            'phoneNo' => ['required', 'string', 'max:20', 'unique:users,phoneNo,' . Auth::user()->userID . ',userID', 'regex:/^0\d{9,11}$/'],
            'address' => 'required|string|max:500',
        ], [
            'email.unique'   => 'This email is already registered.',
            'phoneNo.unique' => 'This phone number is already registered.',
            'phoneNo.regex'  => 'Enter a valid Malaysian phone number.',
        ]);

        $customerData->user->update([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'phoneNo' => $validated['phoneNo'],
        ]);

        $customerData->update([
            'address' => $validated['address'],
        ]);

        session(['customer_name' => $customerData->user->name]);

        return redirect()->route('client.profile.view')->with('success', 'Profile updated successfully!');
    }

    // Change password form
    public function changePasswordForm()
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login first');
        }
        
        $customerData = Customer::with('user')->where('CustomerID', Auth::user()->customer->customerID)->first();
        
        return view('Client.profile.change', ['customerData' => $customerData]);
    }

    // Change password
    public function changePassword(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login first');
        }
    
        $customerData = Customer::with('user')->where('CustomerID', Auth::user()->customer->customerID)->first();
    
        if (!$customerData || !$customerData->user) {
            return back()->withErrors(['old_password' => 'User not found.']);
        }
    
        $request->validate([
            'old_password' => 'required|current_password',
            'new_password' => [
                'required',
                'min:5',
                'max:10',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/'
            ],
            'confirm_password' => 'required|same:new_password',
        ], [
            'old_password.required' => 'Current password is required.',
            'old_password.current_password' => 'Current password does not match.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 5 characters.',
            'new_password.max' => 'New password must not exceed 10 characters.',
            'new_password.regex' => 'New password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'confirm_password.required' => 'Please confirm your new password.',
            'confirm_password.same' => 'The confirmation password does not match.',
        ]);
    
        $customerData->user->password = Hash::make($request->new_password);
        $customerData->user->save();
    
        return back()->with('success', 'Password changed successfully!');
    }
    

    // Upload profile photo
    public function uploadPhoto(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login first');
        }
        $customerData = Customer::where('CustomerID', Auth::user()->customer->customerID)->first();

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file = $request->file('profile_photo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('image'), $filename);

        $customerData->profile_photo = $filename;
        $customerData->save();

        return back()->with('success', 'Profile photo updated successfully!');
    }
}
