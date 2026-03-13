

<?php $__env->startSection('content'); ?>
<div class="payment-success-page">
    <div class="success-card">
        <div class="success-header">
            <div class="success-icon">✔️</div>
            <h1>Payment Successful!</h1>
        </div>

        <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <p class="success-message">
            Thank you for your purchase. Your order has been placed successfully.
        </p>

        <div class="redirect-info">
            You will be redirected to your <a href="<?php echo e(route('client.orders.index')); ?>">Orders Page</a> shortly.
        </div>

        <a href="<?php echo e(route('client.orders.index')); ?>" class="btn-orders">Go to Orders Now</a>
    </div>
</div>

<script>
    // Auto redirect after 3 seconds
    setTimeout(function() {
        window.location.href = "<?php echo e(route('client.orders.index')); ?>";
    }, 3000);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/Client/paymentsuccess.blade.php ENDPATH**/ ?>