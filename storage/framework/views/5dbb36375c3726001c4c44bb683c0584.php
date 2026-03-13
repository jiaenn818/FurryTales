

<?php $__env->startSection('content'); ?>

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
                            <img src="<?php echo e(isset($customerData) && $customerData->profile_photo ? asset('image/' . $customerData->profile_photo) : asset('image/profile.png')); ?>" 
                                 alt="Profile Photo">
                        </div>
                        <?php if(isset($customerData)): ?>
                            <form action="<?php echo e(route('client.profile.uploadPhoto')); ?>" method="POST" enctype="multipart/form-data" class="profile-photo-upload">
                                <?php echo csrf_field(); ?>
                                <label for="profile_photo" class="profile-photo-button" title="Change Photo">
                                    <span>📷</span>
                                </label>
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/*" onchange="this.form.submit()">
                            </form>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-title-section">
                        <h1>My Profile</h1>
                        <?php if(isset($customerData)): ?>
                            <p>Welcome back, <?php echo e($customerData->user->name); ?>!</p>
                        <?php endif; ?>
                    </div>

                    <?php if(isset($customerData)): ?>
                        <a href="<?php echo e(route('client.profile.edit')); ?>" class="profile-edit-button">
                            Edit Profile
                        </a>
                    <?php endif; ?>
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
                </div>

                <!-- Profile Info -->
                <?php if(isset($customerData) && $customerData): ?>
                    <div class="profile-info-grid">
                        <!-- Left Column -->
                        <div class="profile-info-column">
                            <div class="profile-info-card">
                                <div class="profile-info-item">
                                    <label>User ID</label>
                                    <p><?php echo e($customerData->customerID); ?></p>
                                </div>
                            </div>

                            <div class="profile-info-card">
                                <div class="profile-info-item">
                                    <label>Full Name</label>
                                    <p><?php echo e($customerData->user->name); ?></p>
                                </div>
                            </div>

                            <div class="profile-info-card">
                                <div class="profile-info-item">
                                    <label>Email Address</label>
                                    <p><?php echo e($customerData->user->email); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="profile-info-column">
                            <div class="profile-info-card">
                                <div class="profile-info-item">
                                    <label>Phone Number</label>
                                    <p><?php echo e($customerData->user->phoneNo); ?></p>
                                </div>
                            </div>

                            <div class="profile-info-card profile-info-item-full">
                                <div class="profile-info-item">
                                    <label>Delivery Address</label>
                                    <p><?php echo e($customerData->address ?? 'Not provided'); ?></p>
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
                        <a href="<?php echo e(route('client.password.change')); ?>" class="profile-password-link">
                            Change Password
                        </a>
                    </div>

                <?php else: ?>
                    <div class="profile-empty">
                        <p>You are not logged in.</p>
                        <a href="<?php echo e(route('client.login.page')); ?>" class="profile-login-button">
                            Login Now
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/Client/profile/view.blade.php ENDPATH**/ ?>