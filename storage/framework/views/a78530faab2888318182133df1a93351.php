

<?php $__env->startSection('content'); ?>

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
                <?php if(session('success')): ?>
                    <div class="profile-message profile-message-success">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="profile-message profile-message-error">
                        <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="profile-message profile-message-error">
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            
            <form action="<?php echo e(route('client.profile.update')); ?>" method="POST" class="profile-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                
                <div class="profile-form-grid">
                    <!-- Read-Only Field -->
                    <div class="profile-form-readonly">
                        <label>Customer ID</label>
                        <p><?php echo e($customerData->customerID); ?></p>
                    </div>

                    <!-- Name -->
                    <div class="profile-form-field">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo e(old('name', $customerData->user->name ?? '')); ?>" required
                               class="profile-form-input">
                    </div>

                    <!-- Email -->
                    <div class="profile-form-field">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo e(old('email', $customerData->user->email ?? '')); ?>" required
                               class="profile-form-input">
                    </div>

                    <!-- Phone -->
                    <div class="profile-form-field">
                        <label>Phone Number</label>
                        <input type="text" name="phoneNo" value="<?php echo e(old('phoneNo', $customerData->user->phoneNo ?? '')); ?>" required
                               class="profile-form-input">
                    </div>

                    <!-- Address (Full Width) -->
                    <div class="profile-form-field profile-form-field-full">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="4" required
                                  class="profile-form-textarea"><?php echo e(old('address', $customerData->address)); ?></textarea>
                    </div>
                </div>

                <div class="profile-form-actions">
                    <button type="submit" class="profile-form-submit">
                        Save Changes
                    </button>
                    
                    <a href="<?php echo e(route('client.profile.view')); ?>" class="profile-form-cancel">
                        Cancel
                    </a>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/Client/profile/edit.blade.php ENDPATH**/ ?>