@extends('layouts.app')

@section('content')

<div class="profile-page">
    <div class="container">
        <div class="profile-container">
            <div class="profile-card">
            
            <div class="profile-form-header profile-form-header-center">
                <h1>Change Password</h1>
                <p>Ensure your account stays secure.</p>
            </div>

            <form action="{{ route('client.password.update') }}" method="POST" class="profile-form">
                @csrf
                
                @if(session('success'))
                    <div class="profile-message profile-message-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                <!-- Old Password -->
                <div class="profile-form-field">
                    <label for="old_password">Current Password</label>
                    <input type="password" name="old_password" id="old_password" required
                           class="profile-form-input">
                    @error('old_password')
                        <p class="profile-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="profile-form-field">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" required
                           class="profile-form-input">
                    <small style="color: #777; font-size: 0.8rem; display: block; margin-top: 4px;">
                        * 5-10 characters, mixed case, numbers & special characters
                    </small>
                    @error('new_password')
                        <p class="profile-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="profile-form-field">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required
                           class="profile-form-input">
                    @error('confirm_password')
                        <p class="profile-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-form-actions profile-form-actions-center">
                    <button type="submit" class="profile-form-submit profile-form-submit-full">
                        Update Password
                    </button>
                    
                    <div class="profile-form-cancel-wrapper">
                        <a href="{{ route('client.profile.view') }}" class="profile-form-cancel-link">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
