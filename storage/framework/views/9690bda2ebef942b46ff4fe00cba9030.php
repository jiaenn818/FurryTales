<?php
use Carbon\Carbon;
?>



<?php $__env->startSection('title', 'Rider Assignment'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--color-brand-light);
        }

        .admin-header h1 {
            font-family: var(--font-heading);
            color: var(--color-brand-dark);
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        table.table {
            background: #fff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            width: 100%;
        }

        table.table th,
        table.table td {
            padding: 14px 16px;
            font-size: 14px;
        }

        table.table thead th {
            text-transform: uppercase;
            font-weight: bold;
            background: linear-gradient(135deg,
                    var(--color-brand-primary-gradient-start),
                    var(--color-brand-primary-gradient-end));
            color: white;
        }

        table.table tbody tr:hover {
            background-color: #fdf6f5;
            box-shadow: inset 4px 0 0 var(--color-brand-medium);
        }

        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-active {
            background-color: #5a2c2c;
            color: #fff;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <main class="admin-main">
        <div class="container">

            <div class="admin-header">
                <h1>
                    <i class="fas fa-motorcycle"></i>
                    Rider Assignment
                </h1>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Rider ID</th>
                        <th>Postcode</th>
                        <th>Order Count</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $riders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <?php echo e($rider->riderID); ?><br />
                                <small>User ID: <?php echo e($rider->userID); ?></small>
                            </td>
                            <td><?php echo e($rider->postCode); ?></td>
                            <td>
                                <?php echo e($rider->order_count); ?>

                                <?php if($rider->order_count >= 0): ?>
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#riderModal<?php echo e($rider->riderID); ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="riderModal<?php echo e($rider->riderID); ?>" tabindex="-1"
                                        aria-labelledby="riderModalLabel<?php echo e($rider->riderID); ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="riderModalLabel<?php echo e($rider->riderID); ?>">
                                                        Purchases Assigned to <?php echo e($rider->riderID); ?>

                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <strong>Rider's Postcode: <?php echo e($rider->postCode); ?></strong> <br>
                                                    <?php if($rider->purchases->count()): ?>
                                                        <table class="table table-sm table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Purchase ID</th>
                                                                    <th>Customer ID</th>
                                                                    <th>Order Date</th>
                                                                    <th>Total Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $__currentLoopData = $rider->purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <tr>
                                                                        <td><?php echo e($purchase->PurchaseID); ?></td>
                                                                        <td><?php echo e($purchase->customer->name ?? $purchase->CustomerID); ?>

                                                                        </td>
                                                                        <td><?php echo e(\Carbon\Carbon::parse($purchase->OrderDate)->format('Y-m-d H:i')); ?>

                                                                        </td>
                                                                        <td>RM
                                                                            <?php echo e(number_format($purchase->TotalAmount, 2)); ?>

                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </tbody>
                                                        </table>
                                                    <?php else: ?>
                                                        <p>No purchases assigned.</p>
                                                    <?php endif; ?>
                                                    <!-- Add Purchase Form -->
                                                    <div class="mt-3">
                                                        <form
                                                            action="<?php echo e(route('admin.rider.assignPurchase', $rider->riderID)); ?>"
                                                            method="POST">
                                                            <?php echo csrf_field(); ?>
                                                            <p class="text-success mb-2">
                                                                <i class="fas fa-info-circle"></i>
                                                                Suggested deliveries are highlighted in green based on
                                                                postcode similarity.
                                                            </p>

                                                            <div class="input-group">
                                                                <select name="purchaseID" class="form-select" required>
                                                                    <option value="">-- Select Purchase --</option>
                                                                    <?php $__currentLoopData = $rider->unassignedPurchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php
                                                                            // Count leading digit matches
                                                                            $riderPostcode = (string) $rider->postCode;
                                                                            $purchasePostcode =
                                                                                (string) ($purchase->Postcode ?? '');
                                                                            $matchCount = 0;
                                                                            for (
                                                                                $i = 0;
                                                                                $i <
                                                                                min(
                                                                                    strlen($riderPostcode),
                                                                                    strlen($purchasePostcode),
                                                                                );
                                                                                $i++
                                                                            ) {
                                                                                if (
                                                                                    $riderPostcode[$i] ===
                                                                                    $purchasePostcode[$i]
                                                                                ) {
                                                                                    $matchCount++;
                                                                                } else {
                                                                                    break;
                                                                                }
                                                                            }

                                                                            // Determine green intensity (max 5 digits)
                                                                            $greenIntensity = min($matchCount, 5) * 40;
                                                                            $color = "rgb(0,{$greenIntensity},0)";

                                                                            // Calculate how many days ago
                                                                            $daysAgo = (int) Carbon::parse(
                                                                                $purchase->OrderDate,
                                                                            )->diffInDays(Carbon::now());
                                                                            $daysAgoText =
                                                                                $daysAgo === 0
                                                                                    ? 'Today'
                                                                                    : "{$daysAgo} day" .
                                                                                        ($daysAgo > 1 ? 's' : '') .
                                                                                        ' ago';
                                                                        ?>
                                                                        <option value="<?php echo e($purchase->PurchaseID); ?>"
                                                                            style="color:<?php echo e($color); ?>; font-weight: <?php echo e($matchCount > 0 ? 'bold' : 'normal'); ?>">
                                                                            <?php echo e($purchase->PurchaseID); ?> |
                                                                            Customer:
                                                                            <?php echo e($purchase->customer->name ?? $purchase->CustomerID); ?>

                                                                            |
                                                                            RM
                                                                            <?php echo e(number_format($purchase->TotalAmount, 2)); ?>

                                                                            |
                                                                            Postcode: <?php echo e($purchase->Postcode ?? 'N/A'); ?> |
                                                                            <?php echo e($daysAgoText); ?>

                                                                        </option>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </select>
                                                                <button class="btn btn-success" type="submit">Add</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="text-center">No riders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <h4 class="text-warning mb-3" style="margin-top: 80px;">
                Reminder: A rider can only have up to 5 active deliveries. Please check before assigning!
            </h4>

        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/admin/riderAssignment.blade.php ENDPATH**/ ?>