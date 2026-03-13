

<?php $__env->startSection('content'); ?>

<div class="appointments-page">
    
    <!-- Back Button -->
    <a href="<?php echo e(route('pets.show', $pet->PetID)); ?>" class="appointments-back-button">
        <span>&larr;</span> Back
    </a>

    <div class="appointments-container">
        <h1 class="appointments-title">Book an Appointment</h1>

        <!-- Pet Info Card -->
        <div class="appointments-pet-card">
            <div class="appointments-pet-info">
                <img src="<?php echo e(asset($pet->ImageURL1)); ?>" alt="<?php echo e($pet->PetName); ?>" class="appointments-pet-image">
                <div class="appointments-pet-details">
                    <h2><?php echo e($pet->PetName); ?></h2>
                    <p><?php echo e($pet->Breed); ?></p>
                    <p class="meta"><?php echo e($pet->Age); ?> months • <?php echo e($pet->Gender); ?> • <?php echo e($pet->Color); ?></p>
                </div>
            </div>
        </div>

        <!-- Appointment Form -->
        <form id="appointmentForm" action="<?php echo e(route('appointments.store')); ?>" method="POST" class="appointments-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="petID" value="<?php echo e($pet->PetID); ?>">
            <input type="hidden" name="appointmentDateTime" id="appointmentDateTime">

            <!-- Date Selection -->
            <div class="appointments-form-section">
                <?php
                    $minDate = now()->hour >= 18
                        ? now()->addDay()->format('Y-m-d')
                        : now()->format('Y-m-d');
                ?>
                <label for="appointmentDate" class="appointments-form-label">Select Date</label>
                <input type="date" id="appointmentDate" name="appointmentDate" min="<?php echo e($minDate); ?>" 
                       class="appointments-form-input"
                       required>
            </div>

            <!-- Time Slot Selection -->
            <div class="appointments-form-section">
                <label class="appointments-form-label">Select Time Slot</label>
                <p class="appointments-time-note">⏱️ Each viewing session lasts for 30 minutes</p>
                <div id="timeSlotsContainer" class="appointments-time-slots">
                    Please select a date to view available time slots
                </div>
            </div>

            <!-- Method Selection -->
            <div class="appointments-form-section">
                <label class="appointments-form-label">Appointment Method</label>
                <p id="selectedMethodLabel" style="color: #6b7280; font-weight: 500; margin-bottom: 0.5rem;">
                    Selected: <span id="selectedMethodText">None</span>
                </p>
                <div class="appointments-method-options">
                    <label class="appointments-method-option" onclick="selectMethod(this, 'In-Person')">
                        <input type="radio" name="method" value="In-Person" required>
                        <div class="appointments-method-card">
                            <div class="checkmark">
                                <span>✓</span>
                            </div>
                            <div class="icon">🏢</div>
                            <div class="title">In-Person</div>
                            <div class="description">Visit our outlet</div>
                        </div>
                    </label>
                    <label class="appointments-method-option" onclick="selectMethod(this, 'Video Call')">
                        <input type="radio" name="method" value="Video Call" required>
                        <div class="appointments-method-card">
                            <div class="checkmark">
                                <span>✓</span>
                            </div>
                            <div class="icon">📹</div>
                            <div class="title">Video Call</div>
                            <div class="description">Meet online</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="appointments-form-section">
                <h3 class="appointments-form-label">Your Information</h3>
                <div class="appointments-form-grid">
                    <div class="appointments-form-field">
                        <label for="customerName" class="appointments-form-field-label">Name</label>
                        <input type="text" id="customerName" name="customerName" value="<?php echo e($user->name); ?>" 
                               class="appointments-form-field-input"
                               required>
                    </div>
                    <div class="appointments-form-field">
                        <label for="customerPhone" class="appointments-form-field-label">Phone Number</label>
                        <input type="tel" id="customerPhone" name="customerPhone" value="<?php echo e($user->phoneNo); ?>" 
                               class="appointments-form-field-input"
                               required>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="appointments-submit-button" disabled>
                Confirm Appointment
            </button>
        </form>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if(session('success')): ?>
<div id="successMessage" class="appointments-message appointments-message-success">
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div id="errorMessage" class="appointments-message appointments-message-error">
    <?php echo e(session('error')); ?>

</div>
<?php endif; ?>

<script>
let selectedSlot = null;

// Date picker change handler
document.getElementById('appointmentDate').addEventListener('change', function() {
    const date = this.value;
    const petID = '<?php echo e($pet->PetID); ?>';
    
    if (!date) return;
    
    // Fetch available slots
    fetch('<?php echo e(route("appointments.availableSlots")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ date: date, petID: petID })
    })
    .then(response => response.json())
    .then(data => {
        displayTimeSlots(data.slots);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('timeSlotsContainer').innerHTML = '<p style="color: #ef4444;">Error loading time slots. Please try again.</p>';
    });
});

function displayTimeSlots(slots) {
    const container = document.getElementById('timeSlotsContainer');
    
    if (slots.length === 0) {
        container.innerHTML = '<p style="color: #6b7280;">No available slots for this date</p>';
        return;
    }
    
    // Group slots by time period
    const morning = slots.filter(s => {
        const hour = parseInt(s.time.split(':')[0]);
        return hour >= 9 && hour < 12;
    });
    
    const afternoon = slots.filter(s => {
        const hour = parseInt(s.time.split(':')[0]);
        return hour >= 12 && hour < 15;
    });
    
    const evening = slots.filter(s => {
        const hour = parseInt(s.time.split(':')[0]);
        return hour >= 15 && hour < 18;
    });
    
    let html = '<div style="display: flex; flex-direction: column; gap: 2rem;">';
    
    // Helper function to render a time period section
    function renderPeriod(title, icon, slots) {
        if (slots.length === 0) return '';
        
        let section = `
            <div style="border: 2px solid rgba(217, 202, 199, 0.5); border-radius: 1.5rem; padding: 1.5rem; background: linear-gradient(to bottom right, white, rgba(255, 242, 245, 0.2));">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <span style="font-size: 1.875rem;">${icon}</span>
                    <h3 style="font-size: 1.25rem; font-weight: bold; color: var(--color-brand-dark);">${title}</h3>
                    <span style="margin-left: auto; font-size: 0.875rem; color: var(--color-brand-medium); font-weight: 500;">${slots.filter(s => !s.booked).length} available</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
        `;
        
        slots.forEach(slot => {
            if (slot.booked) {
                // Booked slot - disabled with strikethrough
                section += `
                    <button type="button" disabled
                            style="position: relative; padding: 1rem 1.25rem; border-radius: 0.75rem; background-color: #f9fafb; color: #9ca3af; border: 2px solid #e5e7eb; cursor: not-allowed; font-weight: 600; font-size: 1rem;">
                        <span style="text-decoration: line-through;">${slot.display}</span>
                        <span style="position: absolute; top: 0.25rem; right: 0.5rem; font-size: 0.75rem;">🔒</span>
                    </button>
                `;
            } else {
                // Available slot - clickable with hover effect
                section += `
                    <button type="button" onclick="selectSlot('${slot.datetime}', this)"
                            class="time-slot" style="padding: 1rem 1.25rem; border-radius: 0.75rem; background-color: white; border: 2px solid var(--color-brand-light); font-weight: 600; font-size: 1rem; color: var(--color-brand-dark); cursor: pointer; transition: all 0.3s;">
                        ${slot.display}
                    </button>
                `;
            }
        });
        
        section += `
                </div>
            </div>
        `;
        
        return section;
    }
    
    // Render each time period
    html += renderPeriod('Morning', '🌅', morning);
    html += renderPeriod('Afternoon', '☀️', afternoon);
    html += renderPeriod('Evening', '🌆', evening);
    
    html += '</div>';
    container.innerHTML = html;
    
    // Reset selection
    selectedSlot = null;
    document.getElementById('appointmentDateTime').value = '';
    document.getElementById('submitBtn').disabled = true;
}

function selectSlot(datetime, element) {
    // Remove previous selection - reset all slots
    document.querySelectorAll('.time-slot').forEach(btn => {
        btn.style.backgroundColor = '';
        btn.style.color = '';
        btn.style.borderColor = '';
        btn.classList.remove('shadow-lg');
    });
    
    // Mark as selected - use brand-dark color (#5a2c2c)
    element.style.backgroundColor = '#5a2c2c'; // brand-dark
    element.style.color = '#ffffff'; // white
    element.style.borderColor = '#5a2c2c'; // brand-dark
    element.classList.add('shadow-lg');
    
    // Store selection
    selectedSlot = datetime;
    document.getElementById('appointmentDateTime').value = datetime;
    
    // Enable submit button
    document.getElementById('submitBtn').disabled = false;
}

// Handle method selection
function selectMethod(label, value) {
    // Remove previous selection
    document.querySelectorAll('.appointments-method-card').forEach(card => {
        card.style.backgroundColor = '';
        card.style.borderColor = '';
        card.classList.remove('shadow-lg');
    });
    document.querySelectorAll('.appointments-method-card .checkmark').forEach(check => {
        check.style.display = 'none';
    });
    
    // Mark as selected
    const card = label.querySelector('.appointments-method-card');
    const checkmark = label.querySelector('.checkmark');
    const radio = label.querySelector('input[type="radio"]');
    
    card.style.backgroundColor = '#fff2f5'; // brand-soft
    card.style.borderColor = '#5a2c2c'; // brand-dark
    card.classList.add('shadow-lg');
    checkmark.style.display = 'block';
    radio.checked = true;

    // Show selected method text
    const selectedText = document.getElementById('selectedMethodText');
    if (selectedText) selectedText.textContent = value;
}

// Auto-hide messages after 5 seconds
setTimeout(() => {
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');
    if (successMsg) successMsg.style.display = 'none';
    if (errorMsg) errorMsg.style.display = 'none';
}, 5000);
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/appointments/create.blade.php ENDPATH**/ ?>