<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurryTales - Register</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

</head>
<body class="auth-page">

    <div class="register-container">
        
        <!-- Left Side (Info) -->
        <div class="register-left">
            <div class="register-left-decoration"></div>
            
            <h1 class="register-left-title">FurryTales</h1>
            
            <h2 class="register-left-subtitle">
                Time to<br>
                <span class="register-left-highlight">Wag Back In! 🐾</span>
            </h2>
            
            <p class="register-left-description">
                Your next furry friend might just be a click away.
                Already a member? Log in to track orders, book appointments,
                and discover exclusive pet picks.
            </p>
            
            <a href="<?php echo e(route('client.login.page')); ?>" class="register-left-button">
                Log In
            </a>
        </div>

        <!-- Right Side (Form) -->
        <div class="register-right">
            <h2 class="register-right-title">Create New Account</h2>

            <form action="<?php echo e(route('register.submit')); ?>" method="POST" class="register-form">
                <?php echo csrf_field(); ?>

                <div class="register-form-fields">
                    <input type="text" name="custid" placeholder="User ID" required value="<?php echo e(old('custid')); ?>" class="register-input">
                    
                    <input type="text" name="name" placeholder="Name" required value="<?php echo e(old('name')); ?>" class="register-input">
                    
                    <input type="text" name="phonenumber" placeholder="Phone Number" required value="<?php echo e(old('phonenumber')); ?>" class="register-input">
                    
                    <input type="email" name="email" placeholder="Email" required value="<?php echo e(old('email')); ?>" class="register-input">
                    
                    <input type="password" name="password" id="password" placeholder="Password" required class="register-input">
                    <div class="password-requirements" id="passwordRequirements">
                        <div class="requirement" id="length"><i class="fas fa-circle"></i> 5-10 Characters</div>
                        <div class="requirement" id="uppercase"><i class="fas fa-circle"></i> One Uppercase Letter</div>
                        <div class="requirement" id="lowercase"><i class="fas fa-circle"></i> One Lowercase Letter</div>
                        <div class="requirement" id="number"><i class="fas fa-circle"></i> One Number</div>
                        <div class="requirement" id="special"><i class="fas fa-circle"></i> One Special Character</div>
                    </div>
                    
                    <textarea name="address" placeholder="Address" rows="3" class="register-textarea"><?php echo e(old('address')); ?></textarea>
                    <?php echo NoCaptcha::display(); ?>

                    <?php echo NoCaptcha::renderJs(); ?>

                
                </div>
                <!-- Success Message -->
                <?php if(session('success')): ?>
                <div class="auth-alert auth-alert-success">
                    <p class="register-success-title"><?php echo e(session('success')); ?></p>
                    <small>Redirecting to login page in <span id="countdown" class="font-bold">2</span> seconds...</small>
                </div>
                
                <script>
                    let seconds = 2;
                    const countdownElement = document.getElementById('countdown');
                    const interval = setInterval(() => {
                        seconds--;
                        if(countdownElement) countdownElement.textContent = seconds;
                        if (seconds <= 0) {
                            clearInterval(interval);
                            window.location.href = '<?php echo e(route("client.login.page")); ?>';
                        }
                    }, 1000);
                </script>
                <?php endif; ?>

                <!-- Error Messages -->
                <?php if(session('error')): ?>
                <div class="auth-alert auth-alert-error">
                    <?php echo e(session('error')); ?>

                </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                <div class="auth-alert auth-alert-error">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <button type="submit" class="register-button">
                    Create Account
                </button>
            </form>
        </div>
        
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const requirements = {
                length: document.getElementById('length'),
                uppercase: document.getElementById('uppercase'),
                lowercase: document.getElementById('lowercase'),
                number: document.getElementById('number'),
                special: document.getElementById('special')
            };

            const checks = {
                length: (val) => val.length >= 5 && val.length <= 10,
                uppercase: (val) => /[A-Z]/.test(val),
                lowercase: (val) => /[a-z]/.test(val),
                number: (val) => /[0-9]/.test(val),
                special: (val) => /[!@#$%^&*(),.?":{}|<>]/.test(val)
            };

            passwordInput.addEventListener('input', function() {
                const value = passwordInput.value;
                let allMet = true;

                for (const key in checks) {
                    const isMet = checks[key](value);
                    if (isMet) {
                        requirements[key].classList.add('met');
                        requirements[key].querySelector('i').className = 'fas fa-check-circle';
                    } else {
                        requirements[key].classList.remove('met');
                        requirements[key].querySelector('i').className = 'fas fa-circle';
                        allMet = false;
                    }
                }

                if (value.length > 0) {
                    if (allMet) {
                        passwordInput.classList.remove('invalid-password');
                        passwordInput.classList.add('valid-password');
                    } else {
                        passwordInput.classList.remove('valid-password');
                        passwordInput.classList.add('invalid-password');
                    }
                } else {
                    passwordInput.classList.remove('valid-password', 'invalid-password');
                }
            });
        });
    </script>
</body>
</html><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/register.blade.php ENDPATH**/ ?>