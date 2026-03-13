@extends('layouts.admin')

@section('content')

@section('content')

<div class="otp-wrapper">
    <div class="otp-card">
        <h3>Email OTP Verification</h3>

        @if ($errors->any())
            <div class="otp-error">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="otp-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Verify OTP --}}
        <form method="POST" action="{{ route('admin.otp.verify') }}">
            @csrf
            <input type="text" name="otp" placeholder="Enter OTP" required class="otp-input">
            <button type="submit" class="otp-btn primary">Verify OTP</button>
        </form>

        {{-- Resend OTP --}}
        <form method="POST" action="{{ route('admin.otp.resend') }}" id="resendForm">
            @csrf
            <button type="submit" class="otp-btn secondary" id="resendBtn" disabled>
                Resend OTP (<span id="countdown">60</span>s)
            </button>
        </form>
    </div>
</div>

@endsection

@push('styles')

<style>
.otp-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 80px;
}

.otp-card {
    background: white;
    padding: 32px;
    border-radius: 16px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    text-align: center;
    animation: fadeIn 0.4s ease;
}

.otp-card h3 {
    margin-bottom: 20px;
    color: #5a2c2c;
    font-weight: 700;
}

.otp-input {
    width: 100%;
    padding: 14px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    margin-bottom: 16px;
    font-size: 16px;
    text-align: center;
    letter-spacing: 3px;
}

.otp-input:focus {
    border-color: #a95c68;
    outline: none;
    box-shadow: 0 0 0 3px rgba(169, 92, 104, 0.1);
}

.otp-btn {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    margin-bottom: 10px;
    transition: 0.3s;
}

.otp-btn.primary {
    background: linear-gradient(135deg, #a95c68, #d9999b);
    color: white;
}

.otp-btn.primary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.otp-btn.secondary {
    background: #f3f4f6;
    color: #374151;
}

.otp-btn.secondary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.otp-btn.secondary:hover:not(:disabled) {
    background: #e5e7eb;
}

.otp-error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 12px;
}

.otp-success {
    background: #d1fae5;
    color: #065f46;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 12px;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let seconds = 60;
    const countdownEl = document.getElementById('countdown');
    const resendBtn = document.getElementById('resendBtn');

    if (!countdownEl || !resendBtn) return;

    const timer = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;

        if (seconds <= 0) {
            clearInterval(timer);
            resendBtn.disabled = false;
            resendBtn.textContent = 'Resend OTP';
        }
    }, 1000);
});
</script>
@endpush