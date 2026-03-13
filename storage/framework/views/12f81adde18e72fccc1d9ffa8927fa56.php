

<?php $__env->startSection('content'); ?>

<div class="cart-page">
    <div class="container">
        <div class="cart-layout">
        
        <div class="cart-items-section">
            <div class="cart-header">
                <h2>My Cart</h2>
                <span class="cart-item-count"><?php echo e($cartItems->count()); ?> Items</span>
            </div>

            <?php if(session('success')): ?>
                <div class="cart-alert cart-alert-success">
                    <p><?php echo e(session('success')); ?></p>
                    <?php if(session('showOptions')): ?>
                        <div class="cart-alert-links">
                            <a href="<?php echo e(route('client.cart.index')); ?>">View Cart</a>
                            <a href="<?php echo e(url()->previous()); ?>">Continue Browsing</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if(session('info')): ?>
                <div class="cart-alert cart-alert-info">
                    <?php echo e(session('info')); ?>

                </div>
            <?php endif; ?>

            <div class="cart-items-container">
                <div class="cart-header-row">
                    <div></div>
                    <div></div>
                    <div>Product</div>
                    <div>Qty</div>
                    <div>Unit Price</div>
                    <div>Total Price</div>
                    <div></div>
                </div>
              <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isPet = !empty($item->PetID);
                    $product = $isPet ? $item->pet : $item->accessory;

                    $image = $isPet
                        ? ($product->ImageURL1 ?? 'image/default-pet.png')
                        : ($product->ImageURL1 ?? 'image/default-pet.png');

                    $name = $isPet ? $product->PetName : $product->AccessoryName;

                    // Unit price
                    if ($isPet) {
                        $unitPrice = $product->Price;
                    } else {
                        // For accessories, get price from the variant
                        $unitPrice = $item->variant ? $item->variant->Price : $product->Price;
                    }

                    $quantity = $isPet ? 1 : $item->Quantity;
                    $totalPrice = $unitPrice * $quantity;

                    $type = $isPet ? 'pet' : 'accessory';
                    
                    // Get stock quantity for accessories
                    $stockQty = 999;
                    if (!$isPet && $item->VariantID && $item->OutletID) {
                        $outletAccessory = \App\Models\OutletAccessory::where('VariantID', $item->VariantID)
                            ->where('OutletID', $item->OutletID)
                            ->first();
                        $stockQty = $outletAccessory ? $outletAccessory->StockQty : 0;
                    }
                ?>

                <div class="cart-item">
                    <!-- Checkbox -->
                    <div class="cart-col checkbox">
                        <input type="checkbox"
                            class="select-item"
                            value="<?php echo e($item->CartItemID); ?>"
                            data-name="<?php echo e($name); ?>"
                            data-qty="<?php echo e($quantity); ?>"
                            data-price="<?php echo e($totalPrice); ?>"
                            checked>
                    </div>

                    <!-- Image -->
                    <div class="cart-col image">
                        <img src="<?php echo e(asset($image)); ?>" alt="<?php echo e($name); ?>">
                    </div>

                    <!-- Product Info -->
                    <div class="cart-col info">
                        <h3><?php echo e($name); ?></h3>
                        <p><?php echo e($isPet ? $product->Breed : $product->Brand); ?></p>


                        <?php if(!$isPet && $item->variant): ?>
                            <div class="variant-selector">
                                <select class="cart-variant-select" data-cart-item-id="<?php echo e($item->CartItemID); ?>">
                                    <?php if($item->availableVariants): ?>
                                        <?php $__currentLoopData = $item->availableVariants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $availVariant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $variantLabel = collect(explode('|', $availVariant->variant->VariantKey))
                                                    ->map(fn($v) => trim(explode(':', $v)[1] ?? $v))
                                                    ->implode(', ');
                                                $isCurrentVariant = $availVariant->VariantID == $item->VariantID;
                                                $isOutOfStock = $availVariant->StockQty <= 0;
                                            ?>
                                            <option 
                                                value="<?php echo e($availVariant->VariantID); ?>" 
                                                <?php echo e($isCurrentVariant ? 'selected' : ''); ?>

                                                <?php echo e($isOutOfStock ? 'disabled' : ''); ?>

                                                data-stock="<?php echo e($availVariant->StockQty); ?>"
                                            >
                                                <?php echo e($variantLabel); ?><?php echo e($isOutOfStock ? ' (Out of Stock)' : ''); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        <?php endif; ?>


                        <?php if($item->OutletID): ?>
                            <div class="outlet">📍 <?php echo e($item->outlet->City); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Quantity -->
                    <div class="cart-col qty">
                        <?php if($isPet): ?>
                            <?php echo e($quantity); ?>

                        <?php else: ?>
                            <div class="cart-qty-controls" data-cart-item-id="<?php echo e($item->CartItemID); ?>" data-max-stock="<?php echo e($stockQty); ?>">
                                <button class="cart-qty-btn cart-qty-minus" data-action="decrement">&minus;</button>
                                <input type="number" class="cart-qty-input" value="<?php echo e($quantity); ?>" min="1" max="<?php echo e($stockQty); ?>" data-unit-price="<?php echo e($unitPrice); ?>">
                                <button class="cart-qty-btn cart-qty-plus" data-action="increment">+</button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Unit Price -->
                    <div class="cart-col unit">
                        RM <?php echo e(number_format($unitPrice, 2)); ?>

                    </div>

                    <!-- Total -->
                    <div class="cart-col total">
                        RM <?php echo e(number_format($totalPrice, 2)); ?>

                    </div>

                    <!-- Remove -->
                    <div class="cart-col remove">
                        <form action="<?php echo e(route('client.cart.remove', $item->CartItemID)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button class="cart-item-remove">🗑️</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($cartItems->isEmpty()): ?>
                    <div class="cart-empty">
                        <p>Your cart is empty.</p>
                        <a href="<?php echo e(route('client.pets.index')); ?>">Browse Pets</a>
                        <a href="<?php echo e(route('client.accessories.index')); ?>">Browse Accessories</a>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="cart-promo-section">
                <label for="promo-code-input" class="cart-promo-label">Promo Code</label>
                <div class="cart-promo-input-group">
                    <input type="text" id="promo-code-input" class="cart-form-input cart-promo-input" placeholder="Enter code">
                    <button type="button" id="apply-promo-btn" class="cart-apply-btn">
                        Apply
                    </button>
                </div>
                <p id="promo-message" class="promo-message" style="display: none;"></p>
                <button type="button" id="view-vouchers-btn" class="view-vouchers-btn view-vouchers-btn-white">
                    View Available Vouchers
                </button>
            </div>
        </div>

        
        <div class="cart-summary">
            <div class="cart-summary-panel">
                <h2>Order Summary</h2>
                
                <div class="cart-summary-details">
                    <div class="cart-summary-item">
                        <span class="cart-summary-item-label">Items:</span>
                        <ul id="summary-items" class="cart-summary-item-list">
                            <li>-</li>
                        </ul>
                    </div>
                    <div class="cart-summary-item subtotal-row">
                        <span>Subtotal:</span>
                        <span id="summary-price" class="cart-summary-price">RM 0.00</span>
                    </div>
                    <div id="discount-row" class="cart-summary-item discount-row hidden">
                        <span>Discount (<span id="discount-code"></span>):</span>
                        <span id="discount-amount" class="discount-text">-RM 0.00</span>
                    </div>
                    <div class="cart-summary-total-row">
                        <span class="cart-summary-total-label">Final Total:</span>
                        <span id="final-total" class="cart-summary-final-total">RM 0.00</span>
                    </div>
                </div>

                <button id="checkout-btn" class="cart-checkout-button cart-logistics-item">
                    Check Out
                </button>
            </div>

            
            <div class="cart-logistics-container cart-logistics-item">
                
                <div class="cart-address-section">
                    <div class="cart-delivery-section">
                        <h3 class="cart-delivery-label">Delivery Method</h3>
                        <div class="cart-delivery-options">
                            <label class="cart-delivery-option">
                                <input type="radio" name="delivery" value="pickup">
                                <span>I'll pick up</span>
                            </label>
                            <label class="cart-delivery-option">
                                <input type="radio" name="delivery" value="delivery" checked>
                                <span>Deliver to my location</span>
                            </label>
                        </div>
                    </div>
                </div>

                
                <div class="cart-address-section">
                    <div class="cart-form-section">
                        <label class="cart-contact-label">Contact Information</label>
                        <div class="cart-form-grid cart-contact-grid">
                            <input type="text" placeholder="Name" id="contact-name" value="<?php echo e(Auth::user()->name); ?>" class="cart-form-input">
                            <input type="text" placeholder="Phone Number" id="contact-phone" value="<?php echo e(Auth::user()->phoneNo); ?>" class="cart-form-input cart-pickup-fields">
                        </div>
                    </div>
                </div>

                
                <div class="cart-address-section">
                    <!-- Pickup Time (Hidden by default) -->
                    <div id="pickup-fields" class="cart-form-section cart-pickup-fields hidden">
                        <label class="cart-pickup-label">Pickup Details</label>
                        <div class="cart-form-grid cart-pickup-input-group">
                            <input type="text" id="pickup-date" readonly class="cart-form-input cart-pickup-date" placeholder="Pickup Date">
                            <select id="pickup-time" class="cart-form-input cart-pickup-fields">
                                <option value="">Select a time</option>
                            </select>
                        </div>
                        <p class="cart-form-note">Operating hours: 10:00 AM - 9:00 PM</p>
                    </div>

                    <!-- Delivery Fields (Visible by default) -->
                    <div id="delivery-fields" class="cart-form-section cart-delivery-fields">
                        <label class="cart-delivery-label">Delivery Address</label>
                        <textarea placeholder="Address (Street, Unit, etc.)" id="delivery-address" rows="3" class="cart-form-textarea cart-delivery-address"><?php echo e(Auth::user()->customer->address ?? ''); ?></textarea>
                        
                        <div class="cart-form-grid cart-delivery-grid">
                            <input type="text" placeholder="Postcode" id="delivery-postcode" class="cart-form-input cart-delivery-fields">
                            <input type="text" placeholder="State" id="delivery-state" class="cart-form-input cart-delivery-fields">
                        </div>
                    </div>
                </div>
                
                <form id="checkout-form" action="<?php echo e(route('client.payment.checkout')); ?>" method="POST" class="hidden">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="delivery_method" id="form-delivery-method">
                    
                    <input type="hidden" name="contact_name" id="form-contact-name">
                    <input type="hidden" name="contact_phone" id="form-contact-phone">
                    
                    <input type="hidden" name="pickup_date" id="form-pickup-date">
                    <input type="hidden" name="pickup_time" id="form-pickup-time">
                    
                    <input type="hidden" name="delivery_address" id="form-delivery-address">
                    <input type="hidden" name="delivery_postcode" id="form-delivery-postcode">
                    <input type="hidden" name="delivery_state" id="form-delivery-state">
                    
                    <div id="form-pet-ids"></div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>


<div id="vouchers-modal" class="vouchers-modal">
    <div class="vouchers-modal-content">
        <button id="close-vouchers-modal" class="vouchers-modal-close">&times;</button>
        
        <h2 class="vouchers-modal-title">Available Vouchers</h2>
        
        <div id="vouchers-list" class="vouchers-list">
            <?php $__empty_1 = true; $__currentLoopData = $availableVouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="voucher-card" 
                     data-code="<?php echo e($v->voucherID); ?>" 
                     data-min-spend="<?php echo e($v->minSpend); ?>"
                     onclick="selectVoucher('<?php echo e($v->voucherID); ?>', <?php echo e($v->minSpend); ?>)">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <div>
                            <span class="voucher-code"><?php echo e($v->voucherID); ?></span>
                            <span class="voucher-min-spend">Min. Spend RM <?php echo e(number_format($v->minSpend, 2)); ?></span>
                        </div>
                        <span class="voucher-status-badge">
                            ...
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <span class="voucher-amount">RM <?php echo e(number_format($v->discountAmount, 2)); ?> OFF</span>
                        <span class="voucher-expiry">Exp: <?php echo e($v->expiryDate ? $v->expiryDate->format('d M Y') : 'No Expiry'); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p style="text-align: center; color: #6b7280; padding: 2rem;">No vouchers available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// --- Global Voucher State ---
window.appliedDiscount = <?php echo e(session('applied_voucher')['discountAmount'] ?? 0); ?>;
window.appliedVoucherID = '<?php echo e(session('applied_voucher')['voucherID'] ?? ""); ?>';
<?php
    $minSpend = 0;
    if(session('applied_voucher')) {
        $v = \App\Models\Voucher::where('voucherID', session('applied_voucher')['voucherID'])->first();
        if($v) $minSpend = $v->minSpend;
    }
?>
window.appliedMinSpend = <?php echo e($minSpend); ?>;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart DOM Loaded');

    // --- 1. Quantity Control Handlers ---
    document.querySelectorAll('.cart-qty-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const container = this.closest('.cart-qty-controls');
            const input = container.querySelector('.cart-qty-input');
            const currentQty = parseInt(input.value) || 1;
            const maxStock = parseInt(container.dataset.maxStock);
            const action = this.dataset.action;

            let newQty = currentQty;
            if (action === 'increment') {
                newQty = Math.min(currentQty + 1, maxStock);
            } else if (action === 'decrement') {
                newQty = Math.max(currentQty - 1, 1);
            }

            if (newQty !== currentQty) {
                input.value = newQty;
                updateCartQuantity(container, newQty);
            }
        });
    });

    document.querySelectorAll('.cart-qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const container = this.closest('.cart-qty-controls');
            const maxStock = parseInt(container.dataset.maxStock);
            let newQty = parseInt(this.value) || 1;
            newQty = Math.max(1, Math.min(newQty, maxStock));
            this.value = newQty;
            updateCartQuantity(container, newQty);
        });
        input.addEventListener('keypress', function(e) {
            if (e.key < '0' || e.key > '9') e.preventDefault();
        });
    });

    // --- 2. Variant Selector Handler ---
    document.querySelectorAll('.cart-variant-select').forEach(select => {
        select.addEventListener('change', function() {
            const cartItemId = this.dataset.cartItemId;
            const newVariantId = this.value;
            const cartItem = this.closest('.cart-item');
            this.disabled = true;

            fetch('<?php echo e(route("client.cart.updateVariant")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ cart_item_id: cartItemId, variant_id: newVariantId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    cartItem.querySelector('.cart-col.unit').textContent = 'RM ' + parseFloat(data.unit_price).toFixed(2);
                    cartItem.querySelector('.cart-col.total').textContent = 'RM ' + parseFloat(data.total_price).toFixed(2);
                    const qtyControls = cartItem.querySelector('.cart-qty-controls');
                    if (qtyControls) {
                        const qtyInput = qtyControls.querySelector('.cart-qty-input');
                        qtyInput.value = data.quantity;
                        qtyInput.max = data.max_stock;
                        qtyInput.dataset.unitPrice = parseFloat(data.unit_price);
                        qtyControls.dataset.maxStock = data.max_stock;
                    }
                    const checkbox = cartItem.querySelector('.select-item');
                    checkbox.dataset.qty = data.quantity;
                    checkbox.dataset.price = data.total_price;
                    if (data.quantity_adjusted) alert(data.message);
                    updateSummary();
                } else {
                    alert(data.message || 'Failed to update variant');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error updating variant:', error);
                alert('An error occurred while updating variant');
                location.reload();
            })
            .finally(() => { this.disabled = false; });
        });
    });

    // --- 3. Delivery/Pickup Toggles ---
    const deliveryRadios = document.querySelectorAll('input[name="delivery"]');
    deliveryRadios.forEach(radio => radio.addEventListener('change', toggleFields));
    toggleFields(); // Init

    // --- 4. Promo Code & Vouchers Modal Logic ---
    const applyBtn = document.getElementById('apply-promo-btn');
    const viewVouchersBtn = document.getElementById('view-vouchers-btn');
    const vouchersModal = document.getElementById('vouchers-modal');
    const closeVouchersBtn = document.getElementById('close-vouchers-modal');

    if (applyBtn) {
        applyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Apply clicked');
            
            const promoCode = document.getElementById('promo-code-input').value.trim();
            const subtotalText = document.getElementById('summary-price').innerText.replace('RM ', '').replace(/,/g, '');
            const currentTotal = parseFloat(subtotalText);
            const messageEl = document.getElementById('promo-message');

            if (!promoCode) {
                alert('Please enter a promo code.');
                return;
            }

            messageEl.textContent = 'Applying...';
            messageEl.style.display = 'block';
            messageEl.style.color = '#6b7280';

            fetch('<?php echo e(route("client.cart.applyVoucher")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ promo_code: promoCode, cart_total: currentTotal })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.appliedDiscount = parseFloat(data.discount);
                    window.appliedVoucherID = promoCode;
                    window.appliedMinSpend = parseFloat(data.min_spend);
                    
                    document.getElementById('discount-row').style.display = 'flex';
                    document.getElementById('discount-code').textContent = promoCode;
                    document.getElementById('discount-amount').textContent = '-RM ' + window.appliedDiscount.toFixed(2);
                    messageEl.textContent = data.message;
                    messageEl.style.color = '#10b981';
                    updateSummary();
                } else {
                    messageEl.textContent = data.message;
                    messageEl.style.color = '#ef4444';
                    window.appliedDiscount = 0;
                    window.appliedVoucherID = null;
                    window.appliedMinSpend = 0;
                    document.getElementById('discount-row').classList.add('hidden');
                    updateSummary();
                }
            })
            .catch(error => {
                console.error('Error applying voucher:', error);
                messageEl.textContent = 'An error occurred.';
                messageEl.style.color = '#ef4444';
            });
        });
    }

    if (viewVouchersBtn) {
        viewVouchersBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('View Vouchers clicked');
            vouchersModal.classList.remove('hidden');
            vouchersModal.style.display = 'flex';
            updateVoucherBadges();
        });
    }

    if (closeVouchersBtn) {
        closeVouchersBtn.addEventListener('click', () => {
            vouchersModal.classList.add('hidden');
            vouchersModal.style.display = 'none';
        });
    }

    window.addEventListener('click', (e) => {
        if (e.target === vouchersModal) {
            vouchersModal.classList.add('hidden');
            vouchersModal.style.display = 'none';
        }
    });

    // --- 5. Checkout Submit ---
    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            const selectedIds = [];
            document.querySelectorAll('.select-item:checked').forEach(cb => selectedIds.push(cb.value));
            if (selectedIds.length === 0) { alert('Please select at least one item.'); return; }

            const deliveryMethod = document.querySelector('input[name="delivery"]:checked').value;
            const name = document.getElementById('contact-name').value;
            const phone = document.getElementById('contact-phone').value;
            if (!name || !phone) { alert('Please enter contact info.'); return; }

            document.getElementById('form-delivery-method').value = deliveryMethod;
            document.getElementById('form-contact-name').value = name;
            document.getElementById('form-contact-phone').value = phone;

            if (deliveryMethod === 'delivery') {
                const address = document.getElementById('delivery-address').value;
                const postcode = document.getElementById('delivery-postcode').value;
                const state = document.getElementById('delivery-state').value;
                if(!address || !postcode || !state) { alert('Complete delivery info.'); return; }
                document.getElementById('form-delivery-address').value = address;
                document.getElementById('form-delivery-postcode').value = postcode;
                document.getElementById('form-delivery-state').value = state;
            } else {
                const time = document.getElementById('pickup-time').value;
                const date = document.getElementById('pickup-date').value;
                if(!time) { alert('Select pickup time.'); return; }
                document.getElementById('form-pickup-time').value = time;
                document.getElementById('form-pickup-date').value = date;
            }

            const container = document.getElementById('form-pet-ids');
            container.innerHTML = '';
            document.querySelectorAll('.select-item:checked').forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'cart_item_ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('checkout-form').submit();
        });
    }

    // --- 610. Checkbox Change ---
    document.querySelectorAll('.select-item').forEach(cb => {
        cb.addEventListener('change', () => updateSummary());
    });

    updateSummary(); // Final initial call
});

// Helper Functions (outside DOMContentLoaded for global access if needed)
function updateCartQuantity(container, quantity) {
    const cartItemId = container.dataset.cartItemId;
    const input = container.querySelector('.cart-qty-input');
    container.querySelectorAll('.cart-qty-btn').forEach(btn => btn.disabled = true);
    input.disabled = true;

    fetch('<?php echo e(route("client.cart.updateQuantity")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ cart_item_id: cartItemId, quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            container.closest('.cart-item').querySelector('.cart-col.total').textContent = 'RM ' + parseFloat(data.total_price).toFixed(2);
            const checkbox = container.closest('.cart-item').querySelector('.select-item');
            checkbox.dataset.qty = quantity;
            checkbox.dataset.price = data.total_price;
            updateSummary();
        } else {
            alert(data.message || 'Error');
            location.reload();
        }
    })
    .finally(() => {
        container.querySelectorAll('.cart-qty-btn').forEach(btn => btn.disabled = false);
        input.disabled = false;
    });
}

function selectVoucher(code, minSpend) {
    const subtotalText = document.getElementById('summary-price').innerText.replace('RM ', '').replace(/,/g, '');
    const subtotal = parseFloat(subtotalText);
    if (subtotal < minSpend) {
        alert('Needs RM ' + minSpend.toFixed(2));
        return;
    }
    document.getElementById('promo-code-input').value = code;
    const modal = document.getElementById('vouchers-modal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('apply-promo-btn').click();
}

function updateVoucherBadges() {
    const subtotalText = document.getElementById('summary-price').innerText.replace('RM ', '').replace(/,/g, '');
    const subtotal = parseFloat(subtotalText || '0');
    document.querySelectorAll('.voucher-card').forEach(card => {
        const minSpend = parseFloat(card.dataset.minSpend);
        const badge = card.querySelector('.voucher-status-badge');
        if (subtotal >= minSpend) {
            badge.textContent = 'Active';
            badge.style.background = 'var(--color-brand-soft)';
            badge.style.color = 'var(--color-brand-dark)';
            badge.style.border = '1px solid var(--color-brand-light)';
            card.classList.remove('inactive');
        } else {
            badge.textContent = 'Inactive';
            badge.style.background = '#f9fafb';
            badge.style.color = '#9ca3af';
            badge.style.border = '1px solid #e5e7eb';
            card.classList.add('inactive');
        }
    });
}

function toggleFields() {
    const method = document.querySelector('input[name="delivery"]:checked').value;
    const deliveryFields = document.getElementById('delivery-fields');
    const pickupFields = document.getElementById('pickup-fields');
    if (method === 'delivery') {
        deliveryFields.classList.remove('hidden'); pickupFields.classList.add('hidden');
    } else {
        deliveryFields.classList.add('hidden'); pickupFields.classList.remove('hidden');
        generatePickupTimes();
    }
}

function generatePickupTimes() {
    const pickupSelect = document.getElementById('pickup-time');
    const pickupDateInput = document.getElementById('pickup-date');
    if (!pickupSelect || !pickupDateInput) return;
    pickupSelect.innerHTML = '<option value="">Select a time</option>';
    const now = new Date();
    let targetDate = new Date();
    let isTomorrow = (now.getHours() >= 21);
    if (isTomorrow) targetDate.setDate(now.getDate() + 1);
    const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
    pickupDateInput.value = (isTomorrow ? 'Tomorrow, ' : 'Today, ') + targetDate.toLocaleDateString('en-GB', options);
    let start = new Date(targetDate); start.setHours(10, 0, 0, 0);
    const end = new Date(targetDate); end.setHours(21, 0, 0, 0);
    while (start <= end) {
        if (isTomorrow || start > now) {
            const displayTime = start.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
            const value = start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
            const opt = document.createElement('option'); opt.value = value; opt.text = displayTime;
            pickupSelect.appendChild(opt);
        }
        start.setMinutes(start.getMinutes() + 30);
    }
}

function updateSummary() {
    const checkboxes = document.querySelectorAll('.select-item');
    let subtotal = 0;
    let listHTML = '';
    checkboxes.forEach(cb => {
        if (cb.checked) {
            subtotal += parseFloat(cb.dataset.price);
            listHTML += `<li>${cb.dataset.name} × ${cb.dataset.qty}</li>`;
        }
    });
    const list = document.getElementById('summary-items');
    if (list) list.innerHTML = listHTML || '<li>No items selected</li>';
    const priceEl = document.getElementById('summary-price');
    if (priceEl) priceEl.innerText = 'RM ' + subtotal.toLocaleString(undefined, { minimumFractionDigits: 2 });

    // --- Voucher Re-validation Logic ---
    let currentDiscount = 0;
    const discountRow = document.getElementById('discount-row');
    const messageEl = document.getElementById('promo-message');

    if (window.appliedVoucherID && window.appliedMinSpend > 0) {
        if (subtotal < window.appliedMinSpend) {
            // Condition no longer met
            if (discountRow) discountRow.style.display = 'none';
            currentDiscount = 0;
            
            if (messageEl) {
                messageEl.textContent = `Voucher ${window.appliedVoucherID} suspended: RM ${window.appliedMinSpend.toFixed(2)} min spend required.`;
                messageEl.style.display = 'block';
                messageEl.style.color = '#ef4444';
            }
        } else {
            // Condition met
            if (discountRow) {
                discountRow.style.display = 'flex';
                document.getElementById('discount-amount').textContent = '-RM ' + window.appliedDiscount.toFixed(2);
            }
            currentDiscount = window.appliedDiscount;
            
            if (messageEl) {
                messageEl.textContent = 'Voucher applied!';
                messageEl.style.color = '#10b981';
            }
        }
    }

    const finalEl = document.getElementById('final-total');
    if (finalEl) {
        const total = Math.max(0, subtotal - currentDiscount);
        finalEl.innerText = 'RM ' + total.toLocaleString(undefined, { minimumFractionDigits: 2 });
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/Client/cart.blade.php ENDPATH**/ ?>