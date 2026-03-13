@extends('layouts.app')

@section('content')

<div class="profile-page">
    <div class="container">
        <div class="profile-container">
            <div class="profile-card">
            
            <!-- Header Banner / Decoration -->
            <div class="profile-banner">
                <div class="profile-banner-decoration"></div>
            </div>

            <div class="profile-content">
                <!-- Profile Image & Header -->
                <div class="profile-header">
                    <div class="profile-image-wrapper">
                        <div class="profile-image">
                            <img src="{{ isset($customerData) && $customerData->profile_photo ? asset('image/' . $customerData->profile_photo) : asset('image/profile.png') }}" 
                                 alt="Profile Photo">
                        </div>
                        @if(isset($customerData))
                            <form action="{{ route('client.profile.uploadPhoto') }}" method="POST" enctype="multipart/form-data" class="profile-photo-upload">
                                @csrf
                                <label for="profile_photo" class="profile-photo-button" title="Change Photo">
                                    <span>📷</span>
                                </label>
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/*" onchange="this.form.submit()">
                            </form>
                        @endif
                    </div>
                    
                    <div class="profile-title-section">
                        <h1>My Profile</h1>
                        @if(isset($customerData))
                            <p>Welcome back, {{ $customerData->user->name }}!</p>
                        @endif
                    </div>

                    @if(isset($customerData))
                        <a href="{{ route('client.profile.edit') }}" class="profile-edit-button">
                            Edit Profile
                        </a>
                    @endif
                </div>

                <!-- Messages -->
                <div style="width: 100%; margin-bottom: 2rem;">
                    @if(session('success'))
                        <div class="profile-message profile-message-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="profile-message profile-message-error">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                <!-- Profile Info -->
                @if(isset($customerData) && $customerData)
                    <div class="profile-info-grid">
                        <!-- Left Column -->
                        <div class="profile-info-column">
                            <div class="profile-info-card">
                                <div class="profile-info-item">
                                    <label>User ID</label>
                                    <p>{{ $customerData->customerID }}</p>
                                </div>
                            </div>

                            <div class="profile-info-card">
                                <div class="profile-info-item">
                                    <label>Full Name</label>
                                    <p>{{ $customerData->user->name }}</p>
                                </div>
                            </div>

                            <div class="profile-info-card">
                                <div class="profile-info-item">
                                    <label>Email Address</label>
                                    <p>{{ $customerData->user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="profile-info-column">
                            <div class="profile-info-card">
                                <div class="profile-info-item">
                                    <label>Phone Number</label>
                                    <p>{{ $customerData->user->phoneNo }}</p>
                                </div>
                            </div>

                            <div class="profile-info-card profile-info-item-full">
                                <div class="profile-info-item">
                                    <label>Delivery Address</label>
                                    <p>{{ $customerData->address ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="profile-password-section">
                        <div>
                            <label>Password</label>
                            <p>••••••••</p>
                        </div>
                        <a href="{{ route('client.password.change') }}" class="profile-password-link">
                            Change Password
                        </a>
                    </div>

                @else
                    <div class="profile-empty">
                        <p>You are not logged in.</p>
                        <a href="{{ route('client.login.page') }}" class="profile-login-button">
                            Login Now
                        </a>
                    </div>
                @endif
            </div>
            </div>
        </div>
    </div>
</div>
@endsection
