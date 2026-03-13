

<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('css/pet-show.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/accessory-show.css')); ?>">

<div class="pet-show-page">
    
    <!-- Back Button -->
    <a href="<?php echo e(route('client.accessories.index')); ?>" class="pet-show-back-button">
        <span>&larr;</span> Back to Accessories
    </a>

    <div class="pet-show-container">
        
        <!-- Images Section -->
        <div class="pet-show-images">
            <div class="pet-show-header pet-show-header-left">
                <h1><?php echo e($accessory->AccessoryName); ?></h1>
                <div class="pet-show-meta">
                    <span class="pet-show-breed"><?php echo e($accessory->Category); ?></span>
                    <span class="pet-show-divider"></span>
                    <span class="pet-show-price">RM <?php echo e(number_format($accessory->min_price, 2)); ?></span>
                </div>
                <p class="text-brand-medium brand-text">Brand : <?php echo e($accessory->Brand); ?></p>
            </div>

            <div class="pet-show-main-image">
                <!-- Main Image -->
                <img id="mainPhoto" src="<?php echo e(asset($accessory->ImageURL1 ?: 'image/default-pet.png')); ?>" alt="<?php echo e($accessory->AccessoryName); ?>">
                
                <!-- Nav Arrows -->
                <button class="pet-show-nav-button pet-show-nav-left" onclick="changeSlide(-1)">
                    &#10094;
                </button>
                <button class="pet-show-nav-button pet-show-nav-right" onclick="changeSlide(1)">
                    &#10095;
                </button>
            </div>

            <!-- Thumbnails container (JS will populate this) -->
            <div class="pet-show-thumbnails" id="thumbnailsContainer"></div>
            
            <!-- Dots -->
            <div class="pet-show-dots" id="dotsContainer"></div>
        </div>

        <!-- Details Section -->
        <div class="pet-show-details">
            <div class="pet-show-details-card">


                <!-- Flexible Selection Grid (JSON Details) -->
                <?php if(isset($details) && count($details) > 0): ?>
                    <div class="pet-show-selections selection-container">
                        <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="selection-group">
                                <h3 class="selection-header"><?php echo e($label); ?></h3>
                                <div class="selection-options">
                                    <?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="selection-option">
                                            <input type="radio" 
                                                   id="<?php echo e($label); ?>_<?php echo e($index); ?>" 
                                                   name="details[<?php echo e($label); ?>]" 
                                                   value="<?php echo e($option); ?>" 
                                                   data-clean-value="<?php echo e($option); ?>"
                                                   class="detail-radio hidden-radio"
                                                   <?php echo e($index === 0 ? 'checked' : ''); ?>>
                                            <label for="<?php echo e($label); ?>_<?php echo e($index); ?>" 
                                                   class="selection-label">
                                                <?php echo e($option); ?>

                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const variants = <?php echo json_encode($accessory->variants, 15, 512) ?>;
                            const priceDisplay = document.querySelector('.pet-show-price');
                            const radios = document.querySelectorAll('.detail-radio');
                            const outletSelect = document.getElementById('outlet_id');
                            const variantIdInput = document.getElementById('variant_id');
                            const stockDisplayContainer = document.querySelector('.pet-show-info-grid');
                            const initialMinPrice = <?php echo e($accessory->min_price ?? 0); ?>;

                            function getVariantKey() {
                                const selected = [];
                                const groups = new Set();
                                radios.forEach(radio => groups.add(radio.name));
                                
                                groups.forEach(groupName => {
                                    const checked = document.querySelector(`input[name="${groupName}"]:checked`);
                                    if (checked) {
                                        // Use data-clean-value for the DB key match
                                        const label = groupName.replace('details[', '').replace(']', '');
                                        selected.push(`${label}:${checked.dataset.cleanValue}`);
                                    }
                                });
                                return selected.sort().join('|');
                            }

                            function updateUI() {
                                const key = getVariantKey();
                                
                                const variant = variants.find(v => v.VariantKey === key);
                                
                                let finalPrice = initialMinPrice;
                                let maxStock = 0;
                                
                                if (variant) {
                                    finalPrice = parseFloat(variant.Price);
                                    variantIdInput.value = variant.VariantID;
                                } else {
                                    finalPrice = initialMinPrice;
                                    variantIdInput.value = ''; // Clear variant ID if no exact match
                                }

                                priceDisplay.innerText = 'RM ' + finalPrice.toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                                // Update Stock & Outlet Display
                                const currentOutlet = document.querySelector('input[name="outlet_id"]:checked')?.value;
                                
                                if (variant) {
                                    if (variant.outlets && variant.outlets.length > 0) {
                                        let stockHtml = '';
                                        variant.outlets.forEach((vo, idx) => {
                                            const isChecked = vo.OutletID === currentOutlet || (idx === 0 && !currentOutlet);
                                            if (isChecked) {
                                                maxStock = vo.StockQty;
                                            }
                                            stockHtml += `
                                                <div class="outlet-card">
                                                    <input type="radio" name="outlet_id" id="outlet_${vo.OutletID}" value="${vo.OutletID}" ${isChecked ? 'checked' : ''} required class="hidden-radio">
                                                    <label for="outlet_${vo.OutletID}" class="outlet-card-label">
                                                        <span class="outlet-name">${vo.outlet ? vo.outlet.City : vo.OutletID} Outlet</span>
                                                        <span class="outlet-stock ${vo.StockQty > 0 ? 'in-stock' : 'out-of-stock'}">
                                                            ${vo.StockQty > 0 ? vo.StockQty + ' available' : 'Out of Stock'}
                                                        </span>
                                                    </label>
                                                </div>
                                            `;
                                        });
                                        stockDisplayContainer.innerHTML = stockHtml;
                                    } else {
                                        stockDisplayContainer.innerHTML = '<p class="unavailable-message">This item is currently out of stock</p>';
                                    }
                                } else if (variants.length > 0) {
                                    stockDisplayContainer.innerHTML = '<p class="unavailable-message">This specific combination is currently unavailable.</p>';
                                } else {
                                    // General accessory stock if no variants
                                    let stockHtml = '';
                                    const accessoryOutlets = <?php echo json_encode($accessory->outlets, 15, 512) ?>;
                                    accessoryOutlets.forEach((outlet, idx) => {
                                        const isChecked = outlet.OutletID === currentOutlet || (idx === 0 && !currentOutlet);
                                        if (isChecked) {
                                            maxStock = outlet.pivot ? outlet.pivot.StockQty : 0;
                                        }
                                        stockHtml += `
                                            <div class="outlet-card">
                                                <input type="radio" name="outlet_id" id="outlet_${outlet.OutletID}" value="${outlet.OutletID}" ${isChecked ? 'checked' : ''} required class="hidden-radio">
                                                <label for="outlet_${outlet.OutletID}" class="outlet-card-label">
                                                    <span class="outlet-name">${outlet.City} Outlet</span>
                                                    <span class="outlet-stock ${ (outlet.pivot && outlet.pivot.StockQty > 0) ? 'in-stock' : 'out-of-stock'}">
                                                        ${ (outlet.pivot && outlet.pivot.StockQty > 0) ? outlet.pivot.StockQty + ' available' : 'Out of Stock'}
                                                    </span>
                                                </label>
                                            </div>
                                        `;
                                    });
                                    stockDisplayContainer.innerHTML = stockHtml;
                                }
                                
                                // Update quantity input max
                                const qtyInput = document.getElementById('quantity_input');
                                if (qtyInput) {
                                    qtyInput.max = maxStock;
                                    if (parseInt(qtyInput.value) > maxStock) {
                                        qtyInput.value = Math.max(1, maxStock);
                                    }
                                }
                                
                                // Re-attach outlet change listeners
                                document.querySelectorAll('input[name="outlet_id"]').forEach(radio => {
                                    radio.addEventListener('change', updateUI);
                                });
                            }


                            function updateStockMessage(maxStock) {
                                // Redundant since it's already in the outlet card
                            }

                            radios.forEach(radio => {
                                radio.addEventListener('change', updateUI);
                            });

                            // Quantity Controls
                            document.querySelectorAll('.cart-qty-btn').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    const qtyInput = document.getElementById('quantity_input');
                                    const action = this.dataset.action;
                                    let currentQty = parseInt(qtyInput.value) || 1;
                                    const maxStock = parseInt(qtyInput.max);

                                    if (action === 'increment') {
                                        qtyInput.value = Math.min(currentQty + 1, maxStock);
                                    } else if (action === 'decrement') {
                                        qtyInput.value = Math.max(currentQty - 1, 1);
                                    }
                                });
                            });

                            document.getElementById('quantity_input')?.addEventListener('change', function() {
                                const maxStock = parseInt(this.max);
                                let newQty = parseInt(this.value) || 1;
                                this.value = Math.max(1, Math.min(newQty, maxStock));
                            });

                            // Initial call
                            updateUI();
                        });
                    </script>
                <?php endif; ?>

                <!-- Description -->
                <div class="pet-show-description-container">
                    <h3 class="pet-show-description-title">Description</h3>
                    <p class="pet-show-description-text"><?php echo e($accessory->Description); ?></p>
                </div>

                <!-- Availability Section -->
                <div class="availability-section">
                <!-- Outlets & Stock -->
                <div class="pet-show-section">
                    <h3 class="availability-title">Choose Pickup/Delivery Outlet</h3>
                    
                    <form id="addToCartForm" action="<?php echo e(route('client.cart.add', $accessory->AccessoryID)); ?>" method="POST" class="pet-show-action-form action-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="type" value="accessory">
                        <input type="hidden" name="variant_id" id="variant_id" value="">
                        
                        <div class="pet-show-info-grid stock-display-grid">
                            <!-- JS updates this grid -->
                        </div>

                        <!-- Quantity Selector -->
                        <div class="pet-show-section" style="margin-top: 1.5rem;">
                            <h3 class="availability-title">Quantity</h3>
                            <div class="cart-qty-controls" style="max-width: 150px; margin: 0.75rem 0;">
                                <button type="button" class="cart-qty-btn cart-qty-minus" data-action="decrement" style="border: 1px solid #e5e7eb; background: white; padding: 0.5rem 1rem; cursor: pointer; border-radius: 8px 0 0 8px; font-weight: 600; color: #4b5563; transition: all 0.2s;">−</button>
                                <input type="number" name="quantity" id="quantity_input" value="1" min="1" max="999" class="cart-qty-input" style="border: 1px solid #e5e7eb; border-left: none; border-right: none; width: 70px; text-align: center; padding: 0.5rem; font-weight: 600; outline: none;">
                                <button type="button" class="cart-qty-btn cart-qty-plus" data-action="increment" style="border: 1px solid #e5e7eb; background: white; padding: 0.5rem 1rem; cursor: pointer; border-radius: 0 8px 8px 0; font-weight: 600; color: #4b5563; transition: all 0.2s;">+</button>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pet-show-actions actions-container">
                            <button type="submit" class="pet-show-action-button pet-show-action-cart cart-button-full">
                                Add to Cart
                            </button>
                        </div>
                    </form>
                </div>
            </div>        </div>
    </div>
</div>


<div id="cartModal" class="pet-show-modal hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="pet-show-modal-wrapper">
        <div class="pet-show-modal-overlay" aria-hidden="true"></div>
        <div class="pet-show-modal-content">
            <button id="cartModalClose" class="pet-show-modal-close"><span>&times;</span></button>
            <div class="pet-show-modal-body">
                <div class="pet-show-modal-icon"><span>🛍️</span></div>
                <h3 id="cartModalMessage">Added to Cart!</h3>
            </div>
            <div id="cartModalOptions" class="pet-show-modal-options">
                <a href="<?php echo e(route('client.cart.view')); ?>" class="pet-show-modal-button pet-show-modal-button-primary">Go to Cart</a>
                <a href="<?php echo e(route('client.accessories.index')); ?>" class="pet-show-modal-button pet-show-modal-button-secondary">Continue Browsing</a>
            </div>
            <div class="pet-show-modal-close-wrapper">
                <button id="cartModalCloseBtn" class="pet-show-modal-close-button hidden">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let imgs = [
        <?php if($accessory->ImageURL1): ?> "<?php echo e(asset($accessory->ImageURL1)); ?>", <?php endif; ?>
        <?php if($accessory->ImageURL2): ?> "<?php echo e(asset($accessory->ImageURL2)); ?>", <?php endif; ?>
        <?php if($accessory->ImageURL3): ?> "<?php echo e(asset($accessory->ImageURL3)); ?>", <?php endif; ?>
        <?php if($accessory->ImageURL4): ?> "<?php echo e(asset($accessory->ImageURL4)); ?>", <?php endif; ?>
        <?php if($accessory->ImageURL5): ?> "<?php echo e(asset($accessory->ImageURL5)); ?>", <?php endif; ?>
    ].filter(Boolean);

    if (imgs.length === 0) imgs = ["<?php echo e(asset('image/default-pet.png')); ?>"];

    let currentIndex = 0;

    function refreshDisplay() {
        const leftBtn = document.querySelector('.pet-show-nav-left');
        const rightBtn = document.querySelector('.pet-show-nav-right');

        if (imgs.length <= 1) {
            leftBtn.style.display = 'none';
            rightBtn.style.display = 'none';
        } else {
            leftBtn.style.display = 'flex';
            rightBtn.style.display = 'flex';
        }

        const main = document.getElementById('mainPhoto');
        const thumbsContainer = document.getElementById('thumbnailsContainer');
        const dotsContainer = document.getElementById('dotsContainer');

        main.src = imgs[currentIndex];

        // Update Thumbnails
        thumbsContainer.innerHTML = '';
        if (imgs.length > 1) {
            imgs.forEach((img, idx) => {
                const thumb = document.createElement('img');
                thumb.src = img;
                thumb.className = `pet-show-thumbnail ${idx === currentIndex ? 'pet-show-thumbnail-active' : ''}`;
                thumb.addEventListener('click', () => {
                    currentIndex = idx;
                    refreshDisplay();
                });
                thumbsContainer.appendChild(thumb);
            });
            thumbsContainer.style.display = 'flex';
        } else {
            thumbsContainer.style.display = 'none';
        }

        // Update Dots
        dotsContainer.innerHTML = ''; 
        if (imgs.length > 1) {
            for (let i = 0; i < imgs.length; i++) {
                const dot = document.createElement('span');
                dot.className = `pet-show-dot ${i === currentIndex ? 'pet-show-dot-active' : ''}`;
                dot.dataset.index = i;
                dot.addEventListener('click', function() {
                    currentIndex = parseInt(this.dataset.index);
                    refreshDisplay();
                });
                dotsContainer.appendChild(dot);
            }
            dotsContainer.style.display = 'flex';
        } else {
            dotsContainer.style.display = 'none';
        }
    }

    function changeSlide(direction) {
        if (imgs.length <= 1) return;
        currentIndex = (currentIndex + direction + imgs.length) % imgs.length;
        refreshDisplay();
    }

    document.addEventListener('DOMContentLoaded', function() {
        refreshDisplay();

        const modal = document.getElementById('cartModal');
        const message = document.getElementById('cartModalMessage');
        const options = document.getElementById('cartModalOptions');
        const closeBtn = document.getElementById('cartModalCloseBtn');

        function showModal(show) {
            if(show) modal.classList.remove('hidden');
            else modal.classList.add('hidden');
        }

        document.getElementById('addToCartForm').addEventListener('submit', function(e){
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST', body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
            })
            .then(response => response.json())
            .then(data => {
                message.textContent = data.message;
                if(data.status === 'success'){
                    options.style.display = 'flex';
                    closeBtn.style.display = 'none';
                } else {
                    options.style.display = 'none';
                    closeBtn.style.display = 'inline-flex';
                }
                showModal(true);
            })
            .catch(error => { alert('Error adding to cart.'); });
        });

        const closeHandlers = [
            document.getElementById('cartModalClose'),
            document.getElementById('cartModalCloseBtn'),
            modal.querySelector('.pet-show-modal-overlay')
        ];
        
        closeHandlers.forEach(el => {
            if(el) el.addEventListener('click', () => showModal(false));
        });
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/Client/accessory/show.blade.php ENDPATH**/ ?>