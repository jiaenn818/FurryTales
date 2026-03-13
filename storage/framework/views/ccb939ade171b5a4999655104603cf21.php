

<?php $__env->startSection('content'); ?>

<div class="pet-show-page">
    
    <!-- Back Button -->
    <a href="<?php echo e(url()->previous()); ?>" class="pet-show-back-button">
        <span>&larr;</span> Back
    </a>

    <div class="pet-show-container">
        
        <!-- Images Section -->
        <div class="pet-show-images">
            <div class="pet-show-main-image">
                <!-- Main Image -->
                <img id="mainPhoto" src="<?php echo e(asset($pet->ImageURL1)); ?>">
                
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
                <!-- Header -->
                <div class="pet-show-header">
                    <h1><?php echo e($pet->PetName); ?></h1>
                    <div class="pet-show-meta">
                        <span class="pet-show-breed"><?php echo e($pet->Breed); ?></span>
                        <span class="pet-show-divider"></span>
                        <span class="pet-show-price">RM <?php echo e(number_format($pet->Price, 2)); ?></span>
                    </div>
                    <div class="pet-show-outlet">
                        At <?php echo e($pet->outlet->State); ?> Outlet
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="pet-show-info-grid">
                    <div class="pet-show-info-item">
                        <span class="pet-show-info-label">Age</span>
                        <p><?php echo e($pet->Age); ?> Months</p>
                    </div>
                    <div class="pet-show-info-item">
                        <span class="pet-show-info-label">Gender</span>
                        <p><?php echo e($pet->Gender); ?></p>
                    </div>
                    <div class="pet-show-info-item">
                        <span class="pet-show-info-label">Color</span>
                        <p><?php echo e($pet->Color); ?></p>
                    </div>
                    <div class="pet-show-info-item">
                        <span class="pet-show-info-label">Size</span>
                        <p><?php echo e($pet->Size); ?></p>
                    </div>
                    <div class="pet-show-info-item-long">
                        <span class="pet-show-info-label">Description</span>
                        <p><?php echo e($pet->Description ?? 'No description available'); ?></p>
                    </div>
                    <div class="pet-show-info-health">
                        <div class="pet-show-info-item">
                            <span class="pet-show-info-label">Health</span>
                            <div class="pet-show-info-value">
                                <span class="pet-show-check">✓</span>
                                <p><?php echo e($pet->HealthStatus); ?></p>
                            </div>
                        </div>
                        <div class="pet-show-info-item">
                            <span class="pet-show-info-label">Vaccination</span>
                            <?php
                                $vaccinationMuted = isset($pet->VaccinationStatus) && strcasecmp(trim($pet->VaccinationStatus), 'Not vaccinated') === 0;
                            ?>
                            <div class="pet-show-info-value <?php echo e($vaccinationMuted ? 'pet-show-info-value-muted' : ''); ?>">
                                <span class="pet-show-check">✓</span>
                                <p><?php echo e($pet->VaccinationStatus); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pet-show-actions">
                    <a href="<?php echo e(route('client.appointments.create', $pet->PetID)); ?>" class="pet-show-action-button pet-show-action-appointment">
                        Make Appointment
                    </a>

                    <form id="addToCartForm" action="<?php echo e(route('client.cart.add', $pet->PetID)); ?>" method="POST" class="pet-show-action-form">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="pet-show-action-button pet-show-action-cart">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="cartModal" class="pet-show-modal hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="pet-show-modal-wrapper">
        
        <!-- Overlay -->
        <div class="pet-show-modal-overlay" aria-hidden="true"></div>

        <div class="pet-show-modal-content">
            
            <button id="cartModalClose" class="pet-show-modal-close">
                <span>&times;</span>
            </button>
            
            <div class="pet-show-modal-body">
                <div class="pet-show-modal-icon">
                    <span>🐾</span>
                </div>
                <h3 id="cartModalMessage">Added to Cart!</h3>
            </div>
            
            <!-- Success Buttons -->
            <div id="cartModalOptions" class="pet-show-modal-options">
                <a href="<?php echo e(route('client.cart.view')); ?>" class="pet-show-modal-button pet-show-modal-button-primary">
                    Go to Cart
                </a>
                <a href="<?php echo e(url()->previous()); ?>" class="pet-show-modal-button pet-show-modal-button-secondary">
                    Continue Browsing
                </a>
            </div>

            <!-- Error/Info Close Button -->
            <div class="pet-show-modal-close-wrapper">
                <button id="cartModalCloseBtn" class="pet-show-modal-close-button hidden">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // JS Logic Preserved and Adapted
    let imgs = [
        <?php if($pet->ImageURL1): ?> "<?php echo e(asset($pet->ImageURL1)); ?>", <?php endif; ?>
        <?php if($pet->ImageURL2): ?> "<?php echo e(asset($pet->ImageURL2)); ?>", <?php endif; ?>
        <?php if($pet->ImageURL3): ?> "<?php echo e(asset($pet->ImageURL3)); ?>", <?php endif; ?>
        <?php if($pet->ImageURL4): ?> "<?php echo e(asset($pet->ImageURL4)); ?>", <?php endif; ?>
        <?php if($pet->ImageURL5): ?> "<?php echo e(asset($pet->ImageURL5)); ?>", <?php endif; ?>
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

    function showPhotoFromThumb(el) {
        const idx = parseInt(el.dataset.photoIndex);
        if (!Number.isNaN(idx)) {
            currentIndex = idx;
            refreshDisplay();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        refreshDisplay();

        // Modal Logic
        const modal = document.getElementById('cartModal');
        const message = document.getElementById('cartModalMessage');
        const options = document.getElementById('cartModalOptions');
        const closeBtn = document.getElementById('cartModalCloseBtn');

        function showModal(show) {
            if(show) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
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
            .catch(error => { alert('Error adding to cart.'); console.error(error); });
        });

        const closeHandlers = [
            document.getElementById('cartModalClose'),
            document.getElementById('cartModalCloseBtn'),
            modal.querySelector('.pet-show-modal-overlay') // Overlay click
        ];
        
        closeHandlers.forEach(el => {
            if(el) el.addEventListener('click', () => showModal(false));
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/Client/pets/show.blade.php ENDPATH**/ ?>