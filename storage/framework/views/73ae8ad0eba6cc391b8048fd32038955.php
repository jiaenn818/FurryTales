

<?php $__env->startSection('title', 'Your Rides Management'); ?>

<?php $__env->startPush('styles'); ?>

<style>
    :root {
        --primary-color: #4a6fa5;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --light-bg: #f8f9fa;
        --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --card-shadow-hover: 0 8px 15px rgba(0, 0, 0, 0.15);
        --transition-speed: 0.3s;
    }

    .rides-container {
        display: flex;
        flex-wrap: nowrap;
        gap: 20px;
        padding: 20px 0;
        overflow-x: auto;
        min-height: 320px;
    }

    .ride-card {
        flex: 0 0 auto;
        width: 280px;
        background: white;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: all var(--transition-speed) ease;
        overflow: hidden;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        position: relative;
        border: 2px solid transparent;
    }

    .ride-card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-5px);
    }

    .ride-card.active {
        width: 500px;
        border-color: var(--primary-color);
        box-shadow: var(--card-shadow-hover);
        z-index: 10;
    }

    .ride-card.inactive {
        width: 180px;
        opacity: 0.7;
        filter: grayscale(20%);
    }

    .ride-card-header {
        padding: 20px;
        background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
        color: white;
        position: relative;
    }

    .ride-card-header h3 {
        margin: 0 0 8px 0;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background-color: var(--warning-color);
        color: #212529;
    }

    .status-delivered {
        background-color: var(--success-color);
        color: white;
    }

    .status-in-progress {
        background-color: #17a2b8;
        color: white;
    }

    .card-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .basic-info {
        margin-bottom: 15px;
    }

    .detail-info {
        display: none;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .active .detail-info {
        display: block;
        animation: fadeIn 0.5s ease;
    }

    .info-row {
        display: flex;
        margin-bottom: 12px;
        align-items: flex-start;
    }

    .info-label {
        font-weight: 600;
        width: 120px;
        color: #555;
    }

    .info-value {
        flex: 1;
        color: #333;
    }

    .action-container {
        margin-top: auto;
        padding-top: 20px;
        text-align: center;
    }

    .btn-delivered {
        background-color: var(--success-color);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        width: 100%;
    }

    .btn-delivered:hover {
        background-color: #218838;
    }

    .delivered-text {
        color: var(--success-color);
        font-weight: 600;
        font-size: 1.1rem;
        text-align: center;
        padding: 10px;
        background-color: rgba(40, 167, 69, 0.1);
        border-radius: 6px;
    }

    .expand-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.2rem;
    }

    .active .expand-icon {
        transform: translateY(-50%) rotate(180deg);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        width: 100%;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #dee2e6;
    }

    .empty-state h3 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Responsive adjustments */
    /* Responsive adjustments for phones */
    @media (max-width: 768px) {
        .rides-container {
            flex-direction: column;
            /* stack cards vertically */
            overflow-x: visible;
            /* remove horizontal scroll */
            gap: 15px;
        }

        .ride-card {
            width: 100% !important;
            /* full width for cards */
            opacity: 1 !important;
            filter: none !important;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .ride-card.active,
        .ride-card.inactive {
            width: 100% !important;
            /* no shrinking for inactive cards */
        }

        .ride-card .detail-info {
            display: block !important;
            /* always show details on mobile */
            animation: none;
            /* optional: remove fadeIn animation */
        }

        .ride-card .expand-icon {
            display: none;
            /* hide expand/collapse icon on mobile */
        }

        .card-content {
            padding: 15px;
            /* smaller padding for mobile */
        }

        .info-label {
            width: 100px;
            /* slightly smaller label width */
        }

        .btn-delivered {
            width: 100%;
            padding: 10px 15px;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main class="admin-main">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-motorcycle"></i> Your Rides Management</h1>
            <p class="text-muted">Click on any ride to view details and mark as delivered</p>
        </div>

        <?php if($purchases->isEmpty()): ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>No Rides Assigned Yet</h3>
            <p>You don't have any delivery assignments at the moment.</p>
        </div>
        <?php else: ?>
        <div id="weatherBox" style="display:none; margin-top:20px;margin-bottom:20px; padding:15px; background:#f8f9fa; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
            <h4>🌤 Current Location Weather</h4>
            <p id="weatherText">Loading...</p>
        </div>

        <div id="routeContainer" style="display:none; width:100%; height:500px; margin-bottom:20px;">
            <iframe id="routeFrame" width="100%" height="100%" style="border:0;" loading="lazy" allowfullscreen></iframe>
        </div>

        <div class="rides-container" id="ridesContainer">
            <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="ride-card <?php echo e($index === 0 ? 'active' : 'inactive'); ?>"
                data-purchase-id="<?php echo e($purchase->PurchaseID); ?>"
                id="card-<?php echo e($purchase->PurchaseID); ?>">
                <div class="ride-card-header">
                    <h3>
                        Purchase #<?php echo e($purchase->PurchaseID); ?>

                        <span class="status-badge status-<?php echo e(strtolower($purchase->Status) === 'delivered' ? 'delivered' : 'in-progress'); ?>">
                            <?php echo e($purchase->Status); ?>

                        </span>
                    </h3>
                    <p>Customer ID: <?php echo e($purchase->CustomerID); ?></p>
                    <div class="expand-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <div class="card-content">
                    <div class="basic-info">
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value"><?php echo e($purchase->AddressOnly); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Receiver:</span>
                            <span class="info-value"><?php echo e($purchase->Receiver); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value"><?php echo e($purchase->PhoneNumber); ?></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Postcode:</span>
                            <span class="info-value"><?php echo e($purchase->Postcode); ?></span>
                        </div>
                    </div>

                    <div class="detail-info">
                        <div class="info-row">
                            <span class="info-label">Purchase Date:</span>
                            <span class="info-value"><?php echo e($purchase->OrderDate ?? 'Not available'); ?></span>
                        </div>

                        <button class="btn btn-primary" onclick="showRoute('<?php echo e($purchase->AddressOnly); ?>')">
                            View Route
                        </button>
                    </div>

                    <div class="action-container">
                        <?php if(strtolower($purchase->Status) !== 'delivered'): ?>
                        <form action="<?php echo e(route('admin.rider.purchase.delivered', $purchase->PurchaseID)); ?>" method="POST" class="delivery-form">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="btn-delivered">
                                <i class="fas fa-check-circle"></i> Mark as Delivered
                            </button>
                        </form>
                        <?php else: ?>
                        <div class="delivered-text">
                            <i class="fas fa-check-circle"></i> Delivered
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted"><?php echo e(count($purchases)); ?> ride(s) assigned to you</p>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rideCards = document.querySelectorAll('.ride-card');
        const ridesContainer = document.getElementById('ridesContainer');

        // Function to handle card click
        function handleCardClick(event) {
            const clickedCard = event.currentTarget;
            const purchaseId = clickedCard.getAttribute('data-purchase-id');

            // Don't toggle if clicking the delivered button
            if (event.target.closest('.delivery-form')) {
                return;
            }

            // Toggle active state on clicked card
            if (clickedCard.classList.contains('active')) {
                // If already active, just toggle it off
                clickedCard.classList.remove('active');
                clickedCard.classList.add('inactive');
            } else {
                // Remove active class from all cards
                rideCards.forEach(card => {
                    card.classList.remove('active');
                    card.classList.add('inactive');
                });

                // Add active class to clicked card
                clickedCard.classList.remove('inactive');
                clickedCard.classList.add('active');
            }

            // Scroll the active card into view on mobile
            if (window.innerWidth <= 768) {
                clickedCard.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        }

        // Add click event listener to each card
        rideCards.forEach(card => {
            card.addEventListener('click', handleCardClick);
        });

        // Handle form submissions with confirmation
        document.querySelectorAll('.delivery-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to mark this purchase as delivered?')) {
                    e.preventDefault();
                } else {
                    // Show loading state on the button
                    const button = this.querySelector('.btn-delivered');
                    if (button) {
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                        button.disabled = true;
                    }
                }
            });
        });

        // Auto-expand first card if none is active
        if (document.querySelectorAll('.ride-card.active').length === 0 && rideCards.length > 0) {
            rideCards[0].classList.add('active');
            rideCards[0].classList.remove('inactive');
        }

        // Handle window resize for responsive behavior
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth <= 768) {
                    rideCards.forEach(card => {
                        card.classList.remove('inactive');
                    });
                } else {
                    // On desktop, reapply inactive state to non-active cards
                    const activeCard = document.querySelector('.ride-card.active');
                    if (activeCard) {
                        rideCards.forEach(card => {
                            if (card !== activeCard) {
                                card.classList.add('inactive');
                            }
                        });
                    }
                }
            }, 250);
        });
        
    });

    function showRoute(address) {
    // Get rider current location
    navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        const iframeUrl = `https://www.google.com/maps/embed/v1/directions?key=<?php echo e(env('GOOGLE_MAPS_API_KEY')); ?>&origin=${lat},${lng}&destination=${encodeURIComponent(address)}`;

        document.getElementById('routeFrame').src = iframeUrl;
        document.getElementById('routeContainer').style.display = 'block';
        window.scrollTo(0, document.getElementById('routeContainer').offsetTop);
    });
}

</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            fetch(`<?php echo e(route('admin.rider.weather.coords')); ?>?lat=${lat}&lng=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.current) {
                        const weatherBox = document.getElementById('weatherBox');
                        const weatherText = document.getElementById('weatherText');

                        const temp = data.current.temperature;
                        const desc = data.current.weather_descriptions[0];
                        const icon = data.current.weather_icons[0];

                        weatherText.innerHTML = `
                            <img src="${icon}" alt="weather" style="vertical-align:middle;">
                            <strong>${temp}°C</strong> - ${desc}
                        `;

                        weatherBox.style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error('Weather fetch error:', err);
                });
        }, function(error) {
            console.log("Geolocation error:", error);
        });
    } else {
        console.log("Geolocation not supported");
    }
});
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.rider', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/admin/riderJob.blade.php ENDPATH**/ ?>