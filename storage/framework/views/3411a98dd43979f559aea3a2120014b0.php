<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurryTales - Login</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="auth-page">

    <div class="auth-container">
        <div class="auth-inner">
            
            <h1 class="auth-title">FurryTales</h1>
            <h2 class="auth-subtitle">🐾 Welcome Home, Pet Lovers!</h2>

            <form action="<?php echo e(route('login.submit')); ?>" method="POST" class="auth-form">
                <?php echo csrf_field(); ?>

                <div class="auth-input-group">
                    <input type="text" name="custid" placeholder="User ID" required class="auth-input" value="<?php echo e(old('custid', Cookie::get('remember_custid'))); ?>">
                </div>

                <div class="auth-input-group">
                    <input id="password" type="password" name="password" placeholder="Password" required class="auth-input" autocomplete="current-password">
                    <button type="button" id="togglePassword" class="forget-send-button">Show</button>
                </div>
                
                <div class="auth-input-group" style="display: flex; align-items: center; justify-content: center;">
                    <label class="auth-link" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="remember" <?php echo e(Cookie::has('remember_custid') ? 'checked' : ''); ?>>
                        Remember me
                    </label>
                </div>

                <?php if(session('error')): ?>
                <div class="auth-alert auth-alert-error">
                    <?php echo e(session('error')); ?>

                </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                <div class="auth-alert auth-alert-error">
                    <?php echo e($errors->first()); ?>

                </div>
                <?php endif; ?>

                <button type="submit" class="auth-button">
                    Login
                </button>

                <div class="auth-links">
                    <a href="<?php echo e(route('forget.page')); ?>" class="auth-link">
                        Forget Password?
                    </a>
                    <a href="<?php echo e(route('register.page')); ?>" class="auth-link">
                        Don't have an account? Create one
                    </a>
                </div>
            </form>

        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleBtn.textContent = isPassword ? 'Hide' : 'Show';
            });
        }
    </script>

</body>
</html>
<?php /**PATH C:\Users\User\finalyear\resources\views/Client/login.blade.php ENDPATH**/ ?>