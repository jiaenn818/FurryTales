<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Pet</title>
<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>
    <script src="<?php echo e(asset('js/pet-features.js')); ?>"></script>



</head>

<body>

    <?php if($errors->any()): ?>
        <div class="message error">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>


    <a href="<?php echo e(route('admin.pets.index')); ?>" class="back-btn">← Back</a>

    <form method="POST" action="<?php echo e(route('admin.pets.store')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="image_features" id="imageFeaturesInput">


        <div class="card">

            
            <div class="image-upload">
                <label for="imageInput">
                    <img src="<?php echo e(asset('image/default-pet.png')); ?>" id="preview">
                    <p>Upload Image</p>
                </label>
                <input type="file" id="imageInput" name="image" accept="image/*" hidden>
            </div>

            <div class="form">

                <input type="text" value="<?php echo e($nextPetID); ?>" readonly disabled>

                <input type="text" name="petName" placeholder="Name" required>

                <select id="typeInput" name="type" required>
                    <option disabled selected>Select Category</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->category_name); ?>">
                        <?php echo e($category->category_name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <input type="text" id="breedInput" name="breed" placeholder="Breed" required>
                <input type="text" name="color" placeholder="Color">
                <input type="number" name="age" placeholder="Age (month)" min="0" required>
                <input type="number" name="price" placeholder="Price (RM)" min="0" step="0.01" required>

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
                    <option disabled selected>Select Outlet</option>
                    <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($outlet->OutletID); ?>">
                        <?php echo e($outlet->City); ?> - (<?php echo e($outlet->State); ?>)
                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="supplierID" required>
                    <option disabled selected>Select Supplier</option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($supplier->SupplierID); ?>">
                        <?php echo e($supplier->SupplierName); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="size" required>
                    <option disabled selected>Size</option>
                    <option>Small</option>
                    <option>Medium</option>
                    <option>Large</option>
                </select>

                <div class="gender-select">

                    <label class="gender-option">
                        <input type="radio" name="gender" value="Male" required>
                        <img src="/image/male.png" alt="Male">
                        <span>Male</span>
                    </label>

                    <label class="gender-option">
                        <input type="radio" name="gender" value="Female">
                        <img src="/image/female.png" alt="Female">
                        <span>Female</span>
                    </label>

                </div>
            </div>

            <textarea name="description" placeholder="Description"></textarea>

            <!-- Additional Images Section -->
            <div class="additional-images">
                <button type="button" id="addImageBtn">+ Add Another Image</button>
                <div id="extraImagesContainer" class="extra-images-container">
                </div>
            </div>

            <div class="form-submit">
                <button type="submit">Add Pet</button>
                <button type="reset">Reset</button>
            </div>

        </div>
    </form>

    
    <div id="loadingOverlay" style="display:none">Detecting breed…</div>
    <div id="processingOverlay" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); color: white; flex-direction: column; justify-content: center; align-items: center; z-index: 9999;">
        <div class="spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #ff4d6d; border-radius: 50%; width: 40px; height: 40px; animation: spin 2s linear infinite; margin-bottom: 15px;"></div>
        <div id="processingText">Generating image features...</div>
    </div>

    <style>
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* ===============================
               AI BREED DETECTION (MAIN IMAGE)
            =============================== */

            const imageInput = document.getElementById('imageInput');
            const preview = document.getElementById('preview');
            const breedInput = document.getElementById('breedInput');
            const typeInput = document.getElementById('typeInput');
            const overlay = document.getElementById('loadingOverlay');

            imageInput.addEventListener('change', async () => {
                if (!imageInput.files.length) return;

                const file = imageInput.files[0];

                // Show preview
                preview.src = URL.createObjectURL(file);

                const formData = new FormData();
                formData.append('image', file);

                overlay.style.display = 'flex';

                try {
                    const res = await fetch("<?php echo e(route('admin.pets.detectBreed')); ?>", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: formData
                    });

                    const data = await res.json();
                    console.log('AI result:', data);

                    if (data.breed) breedInput.value = data.breed;
                    if (data.type) typeInput.value = data.type;

                    if (data.error) alert(data.error);

                } catch (err) {
                    console.error(err);
                    alert('Breed detection failed');
                } finally {
                    overlay.style.display = 'none';
                }
            });


            /* ===============================
               MULTIPLE IMAGE HANDLING
            =============================== */

            const addImageBtn = document.getElementById("addImageBtn");
            const extraImagesContainer = document.getElementById("extraImagesContainer");

            const maxImages = 5; // total including main image
            let imageCount = 1; // main image already counts as 1

            function createImageInput(index) {
                const imageItem = document.createElement('div');
                imageItem.className = 'image-item';

                const label = document.createElement('label');
                label.htmlFor = `extraImage${index}`;

                const img = document.createElement('img');
                img.src = "<?php echo e(asset('image/default-pet.png')); ?>";
                img.className = 'extra-preview';
                img.alt = `Extra image ${index + 2}`;

                const p = document.createElement('p');
                p.textContent = `Image ${index + 2}`;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-image-btn';
                removeBtn.innerHTML = '×';
                removeBtn.title = 'Remove image';

                const input = document.createElement('input');
                input.type = 'file';
                input.id = `extraImage${index}`;
                input.name = `extraImage${index + 1}`;
                input.accept = 'image/*';
                input.hidden = true;

                // Preview image
                input.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = e => img.src = e.target.result;
                        reader.readAsDataURL(this.files[0]);
                    }
                });

                // Remove image
                removeBtn.addEventListener('click', () => {
                    imageItem.remove();
                    imageCount--;
                    updateAddButton();
                    updateImageNumbers();
                });

                label.appendChild(img);
                label.appendChild(p);

                imageItem.appendChild(label);
                imageItem.appendChild(removeBtn);
                imageItem.appendChild(input);

                return imageItem;
            }

            function updateAddButton() {
                if (imageCount >= maxImages) {
                    addImageBtn.disabled = true;
                    addImageBtn.textContent = 'Maximum 5 images reached';
                } else {
                    addImageBtn.disabled = false;
                    addImageBtn.innerHTML =
                        `+ Add Another Image <span class="image-counter">(${imageCount}/${maxImages})</span>`;
                }
            }

            function updateImageNumbers() {
                const items = document.querySelectorAll('.image-item');
                items.forEach((item, index) => {
                    const p = item.querySelector('p');
                    if (p) p.textContent = `Image ${index + 2}`;
                });
            }

            addImageBtn.addEventListener('click', () => {
                if (imageCount < maxImages) {
                    const index = imageCount - 1;
                    const imageInputBlock = createImageInput(index);
                    extraImagesContainer.appendChild(imageInputBlock);
                    imageCount++;
                    updateAddButton();

                    // Auto open file picker
                    setTimeout(() => {
                        document.getElementById(`extraImage${index}`).click();
                    }, 100);
                }
            });

            // Init button state
            updateAddButton();

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
                    // Collect all files
                    const images = [];
                    if (imageInput.files[0]) images.push(imageInput.files[0]);

                    const extraInputs = document.querySelectorAll('input[name^="extraImage"]');
                    extraInputs.forEach(input => {
                        if (input.files[0]) images.push(input.files[0]);
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

</html><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/admin/pets/petAdd.blade.php ENDPATH**/ ?>