

<?php $__env->startSection('content'); ?>

<div class="profile-page">
    <div class="container">
        <div class="profile-container">
            <div class="profile-card">
            
            <div class="profile-form-header profile-form-header-center">
                <h1>Change Password</h1>
                <p>Ensure your account stays secure.</p>
            </div>

            <form action="<?php echo e(route('client.password.update')); ?>" method="POST" class="profile-form">
                <?php echo csrf_field(); ?>
                
                <?php if(session('success')): ?>
                    <div class="profile-message profile-message-success">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
                
                <!-- Old Password -->
                <div class="profile-form-field">
                    <label for="old_password">Current Password</label>
                    <input type="password" name="old_password" id="old_password" required
                           class="profile-form-input">
                    <?php $__errorArgs = ['old_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="profile-form-error"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- New Password -->
                <div class="profile-form-field">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" required
                           class="profile-form-input">
                    <small style="color: #777; font-size: 0.8rem; display: block; margin-top: 4px;">
                        * 5-10 characters, mixed case, numbers & special characters
                    </small>
                    <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="profile-form-error"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Confirm Password -->
                <div class="profile-form-field">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required
                           class="profile-form-input">
                    <?php $__errorArgs = ['confirm_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="profile-form-error"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="profile-form-actions profile-form-actions-center">
                    <button type="submit" class="profile-form-submit profile-form-submit-full">
                        Update Password
                    </button>
                    
                    <div class="profile-form-cancel-wrapper">
                        <a href="<?php echo e(route('client.profile.view')); ?>" class="profile-form-cancel-link">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/Client/profile/change.blade.php ENDPATH**/ ?>