

<?php $__env->startSection('content'); ?>
<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
<div class="orders-page">
    <div class="container">
        <div class="orders-container">
            <div class="orders-header">
                <h1>My Orders</h1>
                <p>Track and manage your purchase history.</p>
            </div>

          <div class="orders-filter-sort">
    <form method="GET" action="<?php echo e(route('orders.index')); ?>" class="filter-form">
        <div class="filter-group">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status">
                <option value="">All</option>
                <option value="Pending" <?php echo e(request('status') == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                <option value="Picked Up" <?php echo e(request('status') == 'Picked Up' ? 'selected' : ''); ?>>Picked Up</option>
                <option value="Out for Delivery" <?php echo e(request('status') == 'Out for Delivery' ? 'selected' : ''); ?>>Out for Delivery</option>
                <option value="Delivered" <?php echo e(request('status') == 'Delivered' ? 'selected' : ''); ?>>Delivered</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="date">Filter by Date:</label>
            <input type="date" name="date" id="date" value="<?php echo e(request('date')); ?>">
        </div>

        <div class="filter-group">
            <label for="sort_by">Sort By:</label>
            <select name="sort_by" id="sort_by">
                <option value="created_at" <?php echo e(request('sort_by') == 'created_at' ? 'selected' : ''); ?>>Date</option>
                <option value="TotalAmount" <?php echo e(request('sort_by') == 'TotalAmount' ? 'selected' : ''); ?>>Total Amount</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="sort_order">Order:</label>
            <select name="sort_order" id="sort_order">
                <option value="asc" <?php echo e(request('sort_order') == 'asc' ? 'selected' : ''); ?>>Ascending</option>
                <option value="desc" <?php echo e(request('sort_order') == 'desc' ? 'selected' : ''); ?>>Descending</option>
            </select>
        </div>

        <button type="submit" class="filter-submit">Apply</button>
    </form>
</div>


            <?php if($orders->isEmpty()): ?>
                <div class="orders-empty">
                    <div class="orders-empty-icon">📦</div>
                    <h3>No orders found</h3>
                    <p>Looks like you haven't made any purchases yet.</p>
                    <a href="<?php echo e(route('pets.index')); ?>" class="orders-empty-button">
                        Browse Pets
                    </a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="order-card">
                            
                            <!-- Background Decoration -->
                            <div class="order-card-decoration"></div>

                            <div class="order-card-content">
                                
                                <!-- Order Info -->
                                <div class="order-info">
                                    <div class="order-header">
                                        <h3>Order #<?php echo e($order->PurchaseID); ?></h3>
                                        <?php
                                            $statusClass = 'order-status-default';
                                            if ($order->Status === 'Pending') $statusClass = 'order-status-pending';
                                            elseif ($order->Status === 'Picked Up' || $order->Status === 'Delivered') $statusClass = 'order-status-delivered';
                                            elseif ($order->Status === 'Out for Delivery') $statusClass = 'order-status-out-for-delivery';
                                        ?>
                                        <span class="order-status <?php echo e($statusClass); ?>">
                                            <?php echo e($order->Status); ?>

                                        </span>
                                    </div>
                                    <div class="order-meta">
                                        <div class="order-meta-item">
                                            <span class="icon">📅</span>
                                            <?php echo e($order->created_at->format('d M Y, h:i A')); ?>

                                        </div>
                                        <span class="divider"></span>
                                        <div class="order-meta-item">
                                            <span class="icon">💳</span>
                                            MYR <span class="amount"><?php echo e(number_format($order->TotalAmount, 2)); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="order-actions">
                                    <a href="<?php echo e(route('orders.show', $order->PurchaseID)); ?>" class="order-view-button">
                                        View Details
                                        <span>→</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/orders/index.blade.php ENDPATH**/ ?>