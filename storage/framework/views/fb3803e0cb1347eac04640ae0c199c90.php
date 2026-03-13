<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Pet</title>
<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>
    <script src="<?php echo e(asset('js/pet-features.js')); ?>"></script>



</head>

<body>

    <a href="<?php echo e(route('admin.pets.index')); ?>" class="back-btn">← Back</a>

    <form method="POST"
        action="<?php echo e(route('admin.pets.update', $pet)); ?>"
        enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <input type="hidden" name="image_features" id="imageFeaturesInput">


        <div class="card">

            
            <div class="image-upload">
                <label for="imageInput">
                    <img id="preview"
                        src="<?php echo e($pet->ImageURL1 ? asset($pet->ImageURL1) : asset('image/default-pet.png')); ?>">
                    <p>Upload Image</p>
                </label>
                <input type="file" id="imageInput" name="image" accept="image/*" hidden>
            </div>

            
            <div class="form">
                <input type="text" readonly value="<?php echo e($pet->PetID); ?>">

                <input type="text" name="petName" required
                    value="<?php echo e(old('petName', $pet->PetName)); ?>" placeholder="Name">

                <select name="type" required>
                    <option disabled>Select Category</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->category_name); ?>"
                        <?php echo e($pet->Type === $category->category_name ? 'selected' : ''); ?>>
                        <?php echo e($category->category_name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <input type="text" name="breed" required
                    value="<?php echo e(old('breed', $pet->Breed)); ?>" placeholder="Breed">

                <input type="text" name="color"
                    value="<?php echo e(old('color', $pet->Color)); ?>" placeholder="Color">

                <input type="number" name="age" min="0" required
                    value="<?php echo e(old('age', $pet->Age)); ?>" placeholder="Age (month)">

                <input type="number" name="price" min="0" step="0.01" required
                    value="<?php echo e(old('price', $pet->Price)); ?>" placeholder="Price (RM)">

                <select name="healthStatus" required>
                    <option disabled selected>Health Status</option>
                    <option>Excellent</option>
                    <option>Good</option>
                    <option>Fair</option>
                </select>

                <select name="vaccinationStatus" required>
                    <option disabled selected>Vaccination Status</option>
                    <option>Up-to-date</option>
                    <option>Due Soon</option>
                    <option>Overdue</option>
                    <option>Not-vaccinated</option>
                    <option>Partial</option>
                </select>


                <select name="outletID" required>
                    <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($outlet->OutletID); ?>"
                        <?php echo e($pet->OutletID === $outlet->OutletID ? 'selected' : ''); ?>>
                        <?php echo e($outlet->City); ?> - (<?php echo e($outlet->State); ?>)
                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="supplierID" required>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($supplier->SupplierID); ?>"
                        <?php echo e($pet->SupplierID === $supplier->SupplierID ? 'selected' : ''); ?>>
                        <?php echo e($supplier->SupplierName); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="size" required>
                    <?php $__currentLoopData = ['Small','Medium','Large']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($size); ?>"
                        <?php echo e($pet->Size === $size ? 'selected' : ''); ?>>
                        <?php echo e($size); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <div class="gender-select">
                    <label class="gender-option">
                        <input type="radio" name="gender" value="Male"
                            <?php echo e($pet->Gender === 'Male' ? 'checked' : ''); ?>>
                        <img src="<?php echo e(asset('/image/male.png')); ?>"><span>Male</span>
                    </label>

                    <label class="gender-option">
                        <input type="radio" name="gender" value="Female"
                            <?php echo e($pet->Gender === 'Female' ? 'checked' : ''); ?>>
                        <img src="<?php echo e(asset('/image/female.png')); ?>"><span>Female</span>
                    </label>
                </div>
            </div>

            <textarea name="description" rows="4"
                placeholder="Description"><?php echo e(old('description', $pet->Description)); ?></textarea>

            
            <div class="additional-images">
                <button type="button" id="addImageBtn">+ Add Another Image</button>

                <div id="extraImagesContainer" class="extra-images-container">
                    <?php
                    $extraImages = array_values(array_filter([
                    $pet->ImageURL2,
                    $pet->ImageURL3,
                    $pet->ImageURL4,
                    $pet->ImageURL5
                    ]));
                    ?>

                    <?php $__currentLoopData = $extraImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="image-item" data-index="<?php echo e($index); ?>">
                        <label>
                            <img src="<?php echo e(asset($img)); ?>" class="extra-preview">
                            <p>Image <?php echo e($index + 2); ?></p>
                        </label>

                        <!-- REMOVE BUTTON -->
                        <button type="button" class="remove-image-btn">❌</button>

                        <!-- FILE INPUT for uploading a new image -->
                        <input type="file"
                            name="extraImages[<?php echo e($index); ?>]"
                            accept="image/*" hidden>

                        <!-- HIDDEN INPUT that tells backend the old image -->
                        <input type="hidden"
                            name="extraImagesOld[<?php echo e($index); ?>]"
                            value="<?php echo e($img); ?>">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="form-submit">
                <button type="submit" class="submit-btn">Update Pet</button>
                <button type="reset" class="reset-btn">Reset</button>
            </div>

        </div>
    </form>

    <div id="processingOverlay" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); color: white; flex-direction: column; justify-content: center; align-items: center; z-index: 9999;">
        <div class="spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #ff4d6d; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; margin-bottom: 15px;"></div>
        <div id="processingText">Updating image features...</div>
    </div>

    <style>
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>

    

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const mainImageInput = document.getElementById("imageInput");
            const mainPreview = document.getElementById("preview");
            const addBtn = document.getElementById("addImageBtn");
            const container = document.getElementById("extraImagesContainer");
            const maxExtraImages = 4; // ImageURL2, ImageURL3, ImageURL4, ImageURL5

            // ===== MAIN IMAGE PREVIEW =====
            mainImageInput.addEventListener("change", () => {
                if (mainImageInput.files[0]) {
                    mainPreview.src = URL.createObjectURL(mainImageInput.files[0]);
                }
            });

            // ===== FUNCTION TO SET UP IMAGE ITEM =====
            function setupImageItem(item, index) {
                const removeBtn = item.querySelector(".remove-image-btn");
                const fileInput = item.querySelector("input[type=file]");
                const hiddenInput = item.querySelector("input[type=hidden]");
                const img = item.querySelector("img");
                const label = item.querySelector("label");

                // Update index in input names - IMPORTANT: This is the key fix!
                if (fileInput) fileInput.name = `extraImages[${index}]`;
                if (hiddenInput) hiddenInput.name = `extraImagesOld[${index}]`;

                // Update image number display
                const p = item.querySelector("p");
                if (p) p.textContent = `Image ${index + 2}`;

                // Remove button - will trigger reindexing
                removeBtn.addEventListener("click", () => {
                    if (hiddenInput && hiddenInput.value) {
                        // Mark for deletion by setting to special value
                        hiddenInput.value = "DELETE_ME";
                    }
                    item.remove();

                    // Re-index all remaining images
                    reindexAllImages();
                });

                // Click to change image
                if (label && fileInput) {
                    label.addEventListener("click", (e) => {
                        e.preventDefault();
                        fileInput.click();
                    });
                }

                // Preview when file is selected
                if (fileInput && img) {
                    fileInput.addEventListener("change", () => {
                        if (fileInput.files[0]) {
                            img.src = URL.createObjectURL(fileInput.files[0]);
                            // Clear old value since we're uploading new
                            if (hiddenInput) hiddenInput.value = "";
                        }
                    });
                }
            }

            // ===== RE-INDEX ALL IMAGES AFTER REMOVAL =====
            function reindexAllImages() {
                const items = container.querySelectorAll(".image-item");

                items.forEach((item, index) => {
                    // Update all input names with new sequential indices
                    const fileInput = item.querySelector('input[type="file"]');
                    const hiddenInput = item.querySelector('input[type="hidden"]');
                    const p = item.querySelector("p");

                    if (fileInput) fileInput.name = `extraImages[${index}]`;
                    if (hiddenInput) hiddenInput.name = `extraImagesOld[${index}]`;
                    if (p) p.textContent = `Image ${index + 2}`;
                });
            }

            // ===== SETUP EXISTING IMAGES =====
            container.querySelectorAll(".image-item").forEach((item, index) => {
                setupImageItem(item, index);
            });

            // ===== ADD NEW IMAGE =====
            addBtn.addEventListener("click", () => {
                const totalImages = container.querySelectorAll(".image-item").length;
                if (totalImages >= maxExtraImages) {
                    alert(`Maximum ${maxExtraImages} additional images allowed.`);
                    return;
                }

                const index = totalImages;
                const div = document.createElement("div");
                div.className = "image-item";
                div.innerHTML = `
            <label>
                <img class="extra-preview" src="<?php echo e(asset('image/default-pet.png')); ?>">
                <p>Image ${index + 2}</p>
            </label>
            <button type="button" class="remove-image-btn">❌</button>
            <input type="file" name="extraImages[${index}]" accept="image/*" hidden>
            <input type="hidden" name="extraImagesOld[${index}]" value="">
        `;

                container.appendChild(div);
                setupImageItem(div, index);

                // Auto-open file picker
                const fileInput = div.querySelector("input[type=file]");
                if (fileInput) fileInput.click();
            });

            /* ===============================
               FORM SUBMISSION WITH FEATURES
            =============================== */
            const petForm = document.querySelector('form');
            const processingOverlay = document.getElementById('processingOverlay');
            const imageFeaturesInput = document.getElementById('imageFeaturesInput');

            petForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                processingOverlay.style.display = 'flex';

                try {
                    const images = [];
                    
                    // 1. Check main image (new or existing)
                    if (mainImageInput.files[0]) {
                        images.push(mainImageInput.files[0]);
                    } else if (mainPreview.src && !mainPreview.src.includes('default-pet.png')) {
                        images.push(mainPreview.src);
                    }

                    // 2. Check extra images (new or existing)
                    const extraItems = container.querySelectorAll('.image-item');
                    extraItems.forEach(item => {
                        const fileInput = item.querySelector('input[type="file"]');
                        const hiddenInput = item.querySelector('input[type="hidden"]');
                        const img = item.querySelector('img');

                        if (fileInput && fileInput.files[0]) {
                            images.push(fileInput.files[0]);
                        } else if (hiddenInput && hiddenInput.value && hiddenInput.value !== 'DELETE_ME') {
                            // This is an existing image path, convert to full URL for JS loading
                            images.push(window.location.origin + '/' + hiddenInput.value);
                        }
                    });

                    if (images.length > 0) {
                        console.log('Generating features for', images.length, 'images');
                        const features = await generatePetFeatures(images);
                        if (features) {
                            imageFeaturesInput.value = JSON.stringify(features);
                        }
                    }

                    petForm.submit();
                } catch (err) {
                    console.error('Submission failed:', err);
                    alert('An error occurred during feature generation. Please try again.');
                    processingOverlay.style.display = 'none';
                }
            });
        });
    </script>

</body>

</html><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/admin/pets/petEdit.blade.php ENDPATH**/ ?>