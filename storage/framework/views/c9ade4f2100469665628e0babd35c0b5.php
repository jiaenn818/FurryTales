

<?php $__env->startSection('title', 'All Purchases Management'); ?>

<?php $__env->startPush('styles'); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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

    .admin-header h1 i {
        color: var(--color-brand-primary-gradient-start);
    }

    .header-actions {
        display: flex;
        gap: 15px;
        align-items: center;
    }


    /* =========================
   TABLE ENHANCEMENTS
========================= */
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
        letter-spacing: 0.4px;
        text-transform: uppercase;
        font-size: 14px;
        font-weight: bolder;
        background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
        color: white;
    }

    table.table tbody tr {
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }

    table.table tbody tr:hover {
        background-color: #fdf6f5;
        box-shadow: inset 4px 0 0 var(--color-brand-medium);
    }

    /* =========================
   STATUS DROPDOWN
========================= */
    table.table select {
        width: 100%;
        padding: 6px 10px;
        font-size: 13px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background-color: #fff;
        cursor: pointer;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    table.table select:hover {
        border-color: var(--color-brand-medium);
    }

    table.table select:focus {
        outline: none;
        border-color: var(--color-brand-dark);
        box-shadow: 0 0 0 2px rgba(143, 93, 84, 0.2);
    }

    /* =========================
   ITEMS LIST
========================= */
    table.table tbody ul {
        list-style-type: disc;
        padding-left: 18px;
    }

    table.table tbody li {
        font-size: 13px;
        color: #333;
    }

    table.table tbody li strong {
        color: var(--color-brand-dark);
    }

    /* =========================
   SEARCH & CALENDAR
========================= */
    .filters-container {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .filters-container input {
        flex: 1;
        border-radius: 30px;
        padding: 10px 16px;
        border: 1px solid #ccc;
        font-size: 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .filters-container input:focus {
        outline: none;
        border-color: var(--color-brand-medium);
        box-shadow: 0 0 0 3px rgba(143, 93, 84, 0.15);
    }

    .clear-btn-container {
        margin-left: 10px;
    }

    .clear-btn {
        background-color: transparent;
        color: #888;
        font-size: 18px;
        border: none;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .clear-btn:hover {
        color: var(--color-brand-dark);
    }

    /* =========================
   EMPTY STATE
========================= */
    table.table td.text-center {
        font-style: italic;
        color: #777;
        background-color: #fafafa;
    }

    /* =========================
   FLATPICKR STYLING
========================= */
    .flatpickr-calendar {
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        font-family: var(--font-body);
        z-index: 9999;
    }

    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background: var(--color-brand-medium);
        color: #fff;
        border-radius: 50% !important;
    }

    .flatpickr-day:hover {
        background: var(--color-brand-light);
        border-radius: 50%;
    }

    .badge-method {
        font-weight: bolder;
    }

    .badge-method.text-muted {
        font-weight: normal;
        font-size: 13px;
    }

    .btn-method-filter {
        padding: 8px 16px;
        border: 1px solid var(--color-brand-medium);
        border-radius: 20px;
        background-color: #fff;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .btn-method-filter:hover {
        background-color: var(--color-brand-light);
    }

    .btn-method-filter.active {
        background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
        color: #fff;
        border-color: transparent;
    }

    .btn-status-filter {
        padding: 8px 16px;
        border: 1px solid var(--color-brand-medium);
        border-radius: 20px;
        background-color: #fff;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .btn-status-filter:hover {
        background-color: var(--color-brand-light);
    }

    .btn-status-filter.active {
        background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
        color: #fff;
        border-color: transparent;
    }

    .status-completed {
        background-color: #d4edda !important;
        /* light green */
        color: #155724 !important;
        /* dark green text */
        font-weight: bold;
    }

    .status-pending {
        background-color: #fff3cd !important;
        /* light orange */
        color: #856404 !important;
        /* dark orange text */
        font-weight: bold;
    }

    .status-out-for-delivery {
        background-color: #cce5ff !important;
        /* light blue */
        color: #004085 !important;
        /* dark blue text */
        font-weight: bold;
    }

    .status-completed {
        background-color: #d4edda !important;
        /* light green */
        color: #155724 !important;
        /* dark green text */
        font-weight: bold;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main class="admin-content">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-shopping-cart"></i> All Purchases Management</h1>
        </div>

        <div class="filters-container">
            <!-- Search Input -->
            <input type="text" id="purchaseSearch" placeholder="Search by Purchase ID or Customer ID..." autocomplete="off">

            <!-- Date Range Calendar -->
            <input type="text" id="purchaseDateRange" placeholder="Select date range..." autocomplete="off">
        </div>

        <!-- New Row for Method & Status Filters -->
        <div class="filters-buttons-row" style="display:flex; justify-content:center; align-items:center; gap:10px; flex-wrap: wrap; margin-bottom: 20px;">
            <!-- Method Filter Buttons -->
            <div class="method-filter-buttons" style="display:flex; gap:10px;">
                <button type="button" class="btn-method-filter active" data-method="">All Method</button>
                <button type="button" class="btn-method-filter" data-method="PickUp">PickUp</button>
                <button type="button" class="btn-method-filter" data-method="Delivery">Delivery</button>
            </div>

            <!-- Status Filter Buttons -->
            <div class="status-filter-buttons" style="display:flex; gap:10px;">
                <button type="button" class="btn-status-filter active" data-status="">All Status</button>
                <button type="button" class="btn-status-filter" data-status="Pending">Pending</button>
                <button type="button" class="btn-status-filter" data-status="Out for Delivery">Out for Delivery</button>
                <button type="button" class="btn-status-filter" data-status="Picked Up,Delivered">Picked Up / Delivered</button>
            </div>

            <button type="button" class="clear-btn" id="clearPurchaseSearch">
                <i class="fas fa-times"></i>
            </button>
        </div>


        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Purchase ID</th>
                    <th>Customer ID</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Items</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr data-purchase="<?php echo e(strtolower($purchase->PurchaseID)); ?>" data-customer="<?php echo e(strtolower($purchase->CustomerID)); ?>" data-date="<?php echo e(\Carbon\Carbon::parse($purchase->OrderDate)->format('Y-m-d')); ?>">
                    <td><?php echo e($purchase->PurchaseID); ?></td>
                    <td><?php echo e($purchase->customer->name ?? $purchase->CustomerID); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($purchase->OrderDate)->format('Y-m-d H:i')); ?></td>
                    <td>RM <?php echo e(number_format($purchase->TotalAmount, 2)); ?></td>
                    <td>
                        <span class="badge-method">
                            [ <?php echo e($purchase->Method); ?> ]
                        </span>

                        <?php if($purchase->Method === 'Delivery' && $purchase->riderID): ?>
                        <span class="badge-method text-muted" style="margin-left:6px;">
                            Rider: <?php echo e($purchase->riderID); ?>

                        </span>
                        <?php endif; ?>

                        <ul class="mb-0">
                            <?php $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <?php if($item->pet): ?>
                                <strong>Pet:</strong> <?php echo e($item->pet->PetID); ?> -- [<?php echo e($item->pet->PetName); ?>] (<?php echo e($item->Quantity); ?> x RM <?php echo e(number_format($item->Price,2)); ?>)
                                <?php elseif($item->accessory): ?>
                                <strong>Accessory:</strong> <?php echo e($item->accessory->AccessoryID); ?> -- [<?php echo e($item->accessory->AccessoryName); ?>]
                                <?php if($item->variant): ?>
                                - Variant: <?php echo e($item->variant->VariantKey); ?>

                                <?php endif; ?>
                                (<?php echo e($item->Quantity); ?> x RM <?php echo e(number_format($item->Price,2)); ?>)
                                <?php endif; ?>
                                <?php if($item->outlet): ?>
                                <span class="text-muted">[Outlet: <?php echo e($item->outlet->OutletID); ?>]</span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </td>
                    <td>
                        <form action="<?php echo e(route('admin.purchase.updateStatus', $purchase->PurchaseID)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>

                            <?php
                            // Allowed statuses for the dropdown
                            if ($purchase->Method === 'PickUp') {
                            $allowedStatuses = ['Pending', 'Picked Up'];
                            } elseif ($purchase->Method === 'Delivery') {
                            $allowedStatuses = ['Pending', 'Out for Delivery', 'Delivered'];
                            } else {
                            $allowedStatuses = [];
                            }

                            // Determine which status to show as selected
                            $selectedStatus = $purchase->Status;

                            // Only for Delivery with a rider: default to 'Out for Delivery' if current status is Pending
                            if ($purchase->Method === 'Delivery' && $purchase->riderID && $purchase->Status === 'Pending') {
                            $selectedStatus = 'Out for Delivery';
                            }
                            ?>

                            <select name="status" onchange="this.form.submit()">
                                <?php $__currentLoopData = $allowedStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>" <?php echo e($selectedStatus === $status ? 'selected' : ''); ?>>
                                    <?php echo e($status); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </form>
                        <span class="badge-method">
                            <small><?php echo e($purchase->DeliveredDate); ?></small>
                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center">No purchases found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dateRangeInput = document.getElementById('purchaseDateRange');
        const searchInput = document.getElementById('purchaseSearch');
        const clearBtn = document.getElementById('clearPurchaseSearch');
        const methodButtons = document.querySelectorAll('.btn-method-filter');
        const statusButtons = document.querySelectorAll('.btn-status-filter');
        const rows = document.querySelectorAll('table tbody tr');

        let selectedMethod = "";
        let selectedStatus = "";

        // Initialize Flatpickr for date range
        flatpickr(dateRangeInput, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            onChange: applyFilters
        });

        // Main filter function
        function applyFilters() {
            const term = searchInput.value.toLowerCase().trim();
            const range = dateRangeInput.value.split(' to ');
            const from = range[0] || '';
            const to = range[1] || '';

            rows.forEach(row => {
                const purchaseID = row.dataset.purchase || '';
                const customerID = row.dataset.customer || '';
                const orderDate = row.dataset.date || '';
                const purchaseMethod = row.querySelector('.badge-method')?.textContent.replace(/\[|\]/g, '').trim() || '';
                const purchaseStatus = row.querySelector('select')?.value || '';

                const matchText = !term || purchaseID.includes(term) || customerID.includes(term);
                let matchDate = true;
                if (from && to) matchDate = orderDate >= from && orderDate <= to;
                else if (from) matchDate = orderDate >= from;
                else if (to) matchDate = orderDate <= to;

                const matchMethod = !selectedMethod || purchaseMethod === selectedMethod;

                let matchStatus = true;
                if (selectedStatus) {
                    if (selectedStatus === "Picked Up,Delivered") {
                        matchStatus = purchaseStatus === "Picked Up" || purchaseStatus === "Delivered";
                    } else {
                        matchStatus = purchaseStatus === selectedStatus;
                    }
                }

                row.style.display = (matchText && matchDate && matchMethod && matchStatus) ? 'table-row' : 'none';
            });

            // Update colors after filtering
            updateStatusColors();
        }

        // Live search
        searchInput.addEventListener('input', applyFilters);

        // Method filter buttons
        methodButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                methodButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                selectedMethod = btn.dataset.method;
                applyFilters();
            });
        });

        // Status filter buttons
        statusButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                statusButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                selectedStatus = btn.dataset.status;
                applyFilters();
            });
        });

        // Clear all filters
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            dateRangeInput.value = '';
            selectedMethod = '';
            selectedStatus = '';

            methodButtons.forEach(b => b.classList.remove('active'));
            methodButtons[0].classList.add('active');

            statusButtons.forEach(b => b.classList.remove('active'));
            statusButtons[0].classList.add('active');

            rows.forEach(row => row.style.display = 'table-row');

            updateStatusColors();
        });

        // ========================
        // Change color for Delivered / Picked Up
        // ========================
        function updateStatusColors() {
            rows.forEach(row => {
                const select = row.querySelector('select');
                if (!select) return;

                const status = select.value;

                // Remove all previous classes
                select.classList.remove('status-completed', 'status-pending', 'status-out-for-delivery');

                // Apply class based on status
                if (status === 'Delivered' || status === 'Picked Up') {
                    select.classList.add('status-completed');
                } else if (status === 'Pending') {
                    select.classList.add('status-pending');
                } else if (status === 'Out for Delivery') {
                    select.classList.add('status-out-for-delivery');
                }
            });
        }

        // Initial update on page load
        updateStatusColors();

        // Update color whenever the select changes
        rows.forEach(row => {
            const select = row.querySelector('select');
            if (!select) return;

            select.addEventListener('change', () => {
                // Wait a tiny moment because form submission will refresh page
                setTimeout(() => updateStatusColors(), 100);
            });
        });
    });
</script>


<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/admin/viewAllPurchase.blade.php ENDPATH**/ ?>