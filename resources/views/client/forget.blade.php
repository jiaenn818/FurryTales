<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurryTales - Reset Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">

    <div class="auth-container forget-container">
        <div class="auth-inner">
            
            <h1 class="auth-title">FurryTales</h1>
            <h2 class="auth-subtitle">Forgot your paw-sword? 🐶🔐</h2>

            {{-- Step 1: Send OTP --}}
            @if(!session('otp_verified'))
            <form action="{{ route('client.forget.sendOTP') }}" method="POST" class="auth-form">
                @csrf
                <div class="forget-input-wrapper">
                    <input type="text" name="customerID" placeholder="Enter Customer ID" required 
                           value="{{ old('customerID') ?? session('customerID_sent') }}"
                           class="forget-input">
                    
                    <button type="submit" class="forget-send-button">
                        Send OTP
                    </button>
                </div>
            </form>
            @endif

            {{-- Step 2: Verify OTP --}}
            @if(session('reset_otp') && !session('otp_verified'))
            <form action="{{ route('client.forget.verifyOTP') }}" method="POST" class="auth-form">
                @csrf
                <div class="auth-input-group">
                    <input type="text" name="otp" placeholder="Enter 6-digit OTP" required maxlength="6" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                           class="forget-otp-input">
                    
                    <button type="submit" class="auth-button">
                        Verify OTP
                    </button>
                </div>
            </form>
            @endif

            {{-- Step 3: Reset Password --}}
            @if(session('otp_verified') && session('customer_id'))
            <form action="{{ route('client.forget.resetPassword') }}" method="POST" class="auth-form">
                @csrf
                <div class="auth-input-group">
                    <input type="password" name="password" placeholder="New Password" required minlength="6"
                           class="auth-input">
                    <small style="color: #ccc; font-size: 0.8rem; display: block; margin-top: -10px; margin-bottom: 10px;">
                        * 5-10 characters, mixed case, numbers & special characters
                    </small>
                    
                    <input type="password" name="password_confirmation" placeholder="Confirm New Password" required minlength="6"
                           class="auth-input">
                    
                    <button type="submit" class="auth-button">
                        Reset Password
                    </button>
                </div>
            </form>
            @endif

            {{-- Messages --}}
            <div class="auth-messages">
                @if(session('error'))
                <div class="auth-alert auth-alert-error">
                    {{ session('error') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="auth-alert auth-alert-error">
                    {{ $errors->first() }}
                </div>
                @endif

                @if(session('success'))
                <div class="auth-alert auth-alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(isset($successMessage))
                <div class="auth-alert auth-alert-success">
                    {{ $successMessage }} Redirecting in <span id="countdown">2</span> seconds.
                </div>
                <script>
                    let seconds = 2;
                    const countdownElement = document.getElementById('countdown');
                    const interval = setInterval(() => {
                        seconds--;
                        if(countdownElement) countdownElement.textContent = seconds;
                        if (seconds <= 0) {
                            clearInterval(interval);
                            window.location.href = '{{ route("client.login.page") }}';
                        }
                    }, 1000);
                </script>
                @endif
            </div>

            <div class="forget-links">
                <a href="{{ route('client.login.page') }}">
                    <span>←</span> Return to Login
                </a>
                @if(session()->has('reset_otp'))
                    <span class="divider">|</span>
                    <form action="{{ route('client.forget.clearSession') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit">
                            Start Over
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>

</body>
</html>