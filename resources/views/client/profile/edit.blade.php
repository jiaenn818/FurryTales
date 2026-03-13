@extends('layouts.app')

@section('content')

<div class="profile-page">
    <div class="container">
        <div class="profile-container">
            <div class="profile-card">
            
            <div class="profile-form-header">
                <h1>Edit Profile</h1>
                <p>Update your personal information below.</p>
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

                @if($errors->any())
                    <div class="profile-message profile-message-error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            
            <form action="{{ route('client.profile.update') }}" method="POST" class="profile-form">
                @csrf
                @method('PUT')
                
                
                <div class="profile-form-grid">
                    <!-- Read-Only Field -->
                    <div class="profile-form-readonly">
                        <label>Customer ID</label>
                        <p>{{ $customerData->customerID }}</p>
                    </div>

                    <!-- Name -->
                    <div class="profile-form-field">
                        <label>Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $customerData->user->name ?? '') }}" required
                               class="profile-form-input">
                    </div>

                    <!-- Email -->
                    <div class="profile-form-field">
                        <label>Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $customerData->user->email ?? '') }}" required
                               class="profile-form-input">
                    </div>

                    <!-- Phone -->
                    <div class="profile-form-field">
                        <label>Phone Number</label>
                        <input type="text" name="phoneNo" value="{{ old('phoneNo', $customerData->user->phoneNo ?? '') }}" required
                               class="profile-form-input">
                    </div>

                    <!-- Address (Full Width) -->
                    <div class="profile-form-field profile-form-field-full">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="4" required
                                  class="profile-form-textarea">{{ old('address', $customerData->address) }}</textarea>
                    </div>
                </div>

                <div class="profile-form-actions">
                    <button type="submit" class="profile-form-submit">
                        Save Changes
                    </button>
                    
                    <a href="{{ route('client.profile.view') }}" class="profile-form-cancel">
                        Cancel
                    </a>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection