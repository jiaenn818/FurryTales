

<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">

<div class="pets-page">
    <div class="container">
        <div class="pets-layout">

        <!-- Sidebar / Filter Panel -->
        <aside class="pets-sidebar">
            <div class="pets-filter-panel">
                <form id="filterForm" action="<?php echo e(route('client.accessories.index')); ?>" method="GET">
                    
                    <div class="pets-search-wrapper">
                        <input type="text"
                            name="search"
                            placeholder="Search accessories..."
                            value="<?php echo e(request('search')); ?>"
                            class="pets-search-input"
                            autocomplete="off">

                        <button type="submit" class="pets-search-button">
                            🔍
                        </button>
                    </div>

                    <!-- Categories -->
                    <div class="pets-category-section">
                        <h4 class="pets-category-header">
                            <span class="pets-category-title">Categories</span>
                        </h4>
                        <ul class="pets-breed-list">
                            <li class="pets-breed-item">
                                <a href="<?php echo e(route('client.accessories.index', request()->except(['category', 'page']))); ?>"
                                   class="<?php echo e(!request('category') ? 'active' : ''); ?>">
                                   All Categories
                                </a>
                            </li>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="pets-breed-item">
                                    <a href="<?php echo e(route('client.accessories.index', array_merge(request()->query(), ['category' => $category->Category]))); ?>"
                                       class="<?php echo e(request('category') === $category->Category ? 'active' : ''); ?>">
                                       <?php echo e($category->Category); ?>

                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>

                    <!-- Brands -->
                    <div class="pets-category-section">
                        <h4 class="pets-category-header">
                            <span class="pets-category-title">Outlet</span>
                        </h4>
                        <ul class="pets-breed-list">
                            <li class="pets-breed-item">
                                <a href="<?php echo e(route('client.accessories.index', request()->except(['outlet', 'page']))); ?>"
                                   class="<?php echo e(!request('outlet') ? 'active' : ''); ?>">
                                   All Outlet
                                </a>
                            </li>
                            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="pets-breed-item">
                                    <a href="<?php echo e(route('client.accessories.index', array_merge(request()->query(), ['outlet' => $outlet->OutletID]))); ?>"
                                       class="<?php echo e(request('outlet') === $outlet->OutletID ? 'active' : ''); ?>">
                                       <?php echo e($outlet->State); ?>

                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>

                </form>
            </div>
        </aside>

        <!-- Main Content (Grid) -->
        <main class="pets-main">

            <!-- Breadcrumbs -->
            <div class="pets-breadcrumbs">
                <a href="<?php echo e(route('client.home')); ?>">Home</a>
                <span>/</span>
                <span class="active">Browse Accessories</span>
                
                <?php if(request('category')): ?>
                    <span>/</span> <span class="text-brand-medium"><?php echo e(request('category')); ?></span>
                <?php endif; ?>
                <?php if(request('outlet')): ?>
                    <span>/</span> <span class="text-brand-medium"><?php echo e(request('outlet')); ?></span>
                <?php endif; ?>
            </div>

            <!-- Header -->
            <div class="pets-header">
                <div class="pets-header-title">
                    <h2>Pet Accessories</h2>
                    <span><?php echo e($accessories->total()); ?> results found</span>
                </div>
            </div>

            <?php if($accessories->count() > 0): ?>
                <div class="pets-grid">
                    <?php $__currentLoopData = $accessories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accessory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="pets-card">
                            <div class="pets-card-image">
                                <a href="#">
                                    <img src="<?php echo e(asset($accessory->ImageURL1 ?: 'image/default-pet.png')); ?>"
                                         alt="<?php echo e($accessory->AccessoryName); ?>">
                                </a>

                                <div class="pets-card-badge"><?php echo e($accessory->Category); ?></div>
                            </div>

                            <div class="pets-card-content">
                                <h3><?php echo e($accessory->AccessoryName); ?></h3>
                                <p class="pets-card-price"><span class="pets-price-label">From </span>RM <?php echo e(number_format($accessory->min_price, 2)); ?></p>

                                <a href="<?php echo e(route('client.accessories.show', $accessory->AccessoryID)); ?>" class="pets-card-button">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <div class="pets-pagination">
                    <?php echo e($accessories->links()); ?>

                </div>
            <?php else: ?>
                <div class="pets-empty">
                    <p>No accessories found matching your criteria.</p>
                    <a href="<?php echo e(route('client.accessories.index')); ?>">Clear all filters</a>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/Client/accessory/index.blade.php ENDPATH**/ ?>