<?php
    $riderID = auth()->user()->rider->riderID;  
?>

<aside class="admin-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">

        <h3><i class="fas fa-paw"></i> Pet Rider</h3>
        <p>Rider System</p>
    </div>

    <!-- Navigation -->
    <ul class="sidebar-nav">
        <li>
            <a href="<?php echo e(route('admin.rider.jobs')); ?>" class="<?php echo e(($currentPage ?? '') === 'outlets' ? 'active' : ''); ?>">
                <span><i class="fas fa-motorcycle"></i>Manage your Rides</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?php echo e(strtoupper(substr(auth()->user()->name ?? 'R',0,1))); ?></div>
            <div class="user-details">
                <h4><?php echo e($riderID); ?></h4>
                <p>Rider</p>
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
<?php $__env->stopPush(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/layouts/rider-sidebar.blade.php ENDPATH**/ ?>