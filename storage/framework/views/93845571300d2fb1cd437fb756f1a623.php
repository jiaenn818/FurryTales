

<?php $__env->startSection('content'); ?>
<div class="reviews-page container">
    <h1>Order Reviews</h1>

    <!-- Show success / error -->
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <h2>Your Orders</h2>

    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="order-review-card">
            <h3>Order #<?php echo e($order->PurchaseID); ?> (<?php echo e($order->Status); ?>)</h3>

            <?php
                $reviewed = $order->orderRating ?? null;
            ?>

            <?php if($reviewed): ?>
                <div class="review-details">
                    <div class="reviewer-info">
                        <?php
                            $customer = $reviewed->customer ?? ($reviewed->purchase->customer ?? null);
                            $profilePhoto = $customer->profile_photo ?? 'profile.png';
                            $reviewerName = $customer->user->name ?? 'Anonymous';
                        ?>
                        <img src="<?php echo e(asset('image/' . $profilePhoto)); ?>" class="reviewer-photo">
                        <span class="reviewer-name"><?php echo e($reviewerName); ?></span>
                        <span class="review-date"><?php echo e($reviewed->created_at->format('d M Y')); ?></span>
                    </div>
                    <p>⭐ Rating: <?php echo e($reviewed->rating); ?>/5</p>
                    <p><?php echo e($reviewed->review); ?></p>
                </div>
            <?php elseif(in_array($order->Status, ['Picked Up', 'Delivered'])): ?>
                <form action="<?php echo e(route('client.reviews.store')); ?>" method="POST" class="review-form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="PurchaseID" value="<?php echo e($order->PurchaseID); ?>">
                    <label>Rating:</label>
                    <select name="rating" required>
                        <option value="">Select</option>
                        <?php for($i=1;$i<=5;$i++): ?>
                            <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                        <?php endfor; ?>
                    </select>
                    <label>Review:</label>
                    <textarea name="review" placeholder="Optional review..."></textarea>
                    <button type="submit">Submit Review</button>
                </form>
            <?php else: ?>
                <p class="cannot-review-text">You cannot review this order yet.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="orders-empty">
            <div class="orders-empty-icon">📦</div>
            <h3>No orders found</h3>
            <p>Looks like you haven't made any purchases yet.</p>
            <a href="<?php echo e(route('client.pets.index')); ?>" class="orders-empty-button">
                Browse Pets
            </a>
        </div>
    <?php endif; ?>


    <h2>All Reviews</h2>

    <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="review-card">
            <div class="reviewer-info">
                <?php
                    $customer = $review->purchase->customer ?? ($review->purchase->customer ?? null);
                    $profilePhoto = $customer->profile_photo ?? 'profile.png';
                    $reviewerName = $customer->user->name ?? 'Anonymous';
                ?>
                <img src="<?php echo e(asset('image/' . $profilePhoto)); ?>" class="reviewer-photo">
                <span class="reviewer-name"><?php echo e($reviewerName); ?></span>
                <span class="review-date"><?php echo e($review->created_at->format('d M Y')); ?></span>
            </div>
            <h4>Order #<?php echo e($review->PurchaseID); ?></h4>
            <p>⭐ <?php echo e($review->rating); ?>/5</p>
            <p><?php echo e($review->review); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="orders-empty">
            <div class="orders-empty-icon">⭐</div>
            <h3>No reviews found</h3>
            <p>No reviews have been submitted yet.</p>
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/Client/reviews.blade.php ENDPATH**/ ?>