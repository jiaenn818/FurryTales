<aside class="admin-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <h3><i class="fas fa-paw"></i> FurryTales</h3>
        <p>Management System</p>
    </div>

    <!-- Navigation -->
    <ul class="sidebar-nav">
        <li>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(($currentPage ?? '') === 'dashboard' ? 'active' : ''); ?>">
                <span><i class="fas fa-tachometer-alt"></i> Dashboard</span>
            </a>
        </li>

        <li>
            <a href="#" id="manage-link" class="<?php echo e(in_array($currentPage ?? '', ['pets','orders','users']) ? 'active' : ''); ?>">
                <span><i class="fas fa-th-large"></i> Manage</span>
                <i class="fas fa-chevron-right chevron"></i>
            </a>
            <ul class="manage-submenu <?php echo e(in_array($currentPage ?? '', ['pets','orders','users']) ? 'open' : ''); ?>" id="manage-submenu">
                <li>
                    <a href="<?php echo e(route('admin.pets.index')); ?>" class="<?php echo e(($currentPage ?? '') === 'pets' ? 'active' : ''); ?>">
                        <i class="fas fa-dog"></i> Pets
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('admin.accessories.index')); ?>" class="<?php echo e(($currentPage ?? '') === 'pets' ? 'active' : ''); ?>">
                        <i class="fa-sharp fa-solid fa-bone"></i> Accessories
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('admin.purchases')); ?>" class="<?php echo e(($currentPage ?? '') === 'orders' ? 'active' : ''); ?>">
                        <i class="fas fa-shopping-cart"></i> Purchases
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('admin.users')); ?>" class="<?php echo e(request()->is('admin/users*') ? 'active' : ''); ?>">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('admin.voucher.index')); ?>" class="<?php echo e(($currentPage ?? '') === 'voucher' ? 'active' : ''); ?>">
                        <i class="fas fa-ticket"></i> Vouchers
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="<?php echo e(route('admin.appointments.index')); ?>" class="<?php echo e(($currentPage ?? '') === 'appointments' ? 'active' : ''); ?>">
                <span><i class="fas fa-calendar-alt"></i> Appointments</span>
                <?php if(!empty($appointmentCount) && $appointmentCount > 0): ?>
                    <span class="badge"><?php echo e($appointmentCount); ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li>
            <a href="<?php echo e(route('admin.rider.assignment')); ?>" class="<?php echo e(($currentPage ?? '') === 'outlets' ? 'active' : ''); ?>">
                <span><i class="fas fa-motorcycle"></i> Rider Assignment</span>
            </a>
        </li>
        <li>
            <a href="<?php echo e(route('admin.outlets.index')); ?>" class="<?php echo e(($currentPage ?? '') === 'outlets' ? 'active' : ''); ?>">
                <span><i class="fas fa-shop"></i> Outlet</span>
            </a>
        </li>

        <li>
            <a href="<?php echo e(route('admin.suppliers.index')); ?>" class="<?php echo e(($currentPage ?? '') === 'outlets' ? 'active' : ''); ?>">
                <span><i class="fas fa-truck"></i> Suppliers</span>
            </a>
        </li>

        <li>
            <a href="<?php echo e(route('admin.reports')); ?>" class="<?php echo e(($currentPage ?? '') === 'reports' ? 'active' : ''); ?>">
                <span><i class="fas fa-chart-bar"></i> Reports</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?php echo e(strtoupper(substr(auth()->user()->name ?? 'A',0,1))); ?></div>
            <div class="user-details">
                <h4><?php echo e(auth()->user()->name ?? 'Admin User'); ?></h4>
                <p><?php echo e(auth()->user()->staff -> Role ?? 'Administrator'); ?></p>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button class="logout-btn" type="submit">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const manageLink = document.getElementById('manage-link');
    const manageSubmenu = document.getElementById('manage-submenu');
    const chevron = document.querySelector('#manage-link .chevron');
    if(manageLink){
        manageLink.addEventListener('click', function(e){
            e.preventDefault();
            manageSubmenu.classList.toggle('open');
            chevron.classList.toggle('open');
        });
    }
});
</script>
<?php $__env->stopPush(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/layouts/admin-sidebar.blade.php ENDPATH**/ ?>