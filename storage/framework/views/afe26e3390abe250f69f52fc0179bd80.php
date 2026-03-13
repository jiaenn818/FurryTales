    

    <?php $__env->startSection('title', 'Add New Accessory'); ?>


    <?php $__env->startPush('styles'); ?>

    <?php $__env->stopPush(); ?>
    <?php $__env->startSection('content'); ?>
    <main class="admin-main">
        <div class="add-header">
            <h1><i class="fas fa-plus-circle"></i> Add New Accessory</h1>
            <p>Create a new accessory with details, images, variants, and stock information</p>
            <a href="<?php echo e(route('admin.accessories.index')); ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.accessories.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <div class="form-tabs">
                <button type="button" class="tab-btn active" data-tab="basic">Basic Info</button>
                <button type="button" class="tab-btn" data-tab="images">Images</button>
                <button type="button" class="tab-btn" data-tab="variants">Variants</button>
            </div>

            
            <div id="basic" class="tab-content active">
                <div class="form-row">
                    <div class="form-group">
                        <label>Accessory ID</label>
                        <input type="text" name="AccessoryID" value="<?php echo e(old('AccessoryID', $nextAccessoryID)); ?>" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Accessory Name*</label>
                        <input type="text" name="AccessoryName" value="<?php echo e(old('AccessoryName')); ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category*</label>
                        <select name="Category" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php $__currentLoopData = $categoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat); ?>" <?php echo e(old('Category')==$cat?'selected':''); ?>><?php echo e($cat); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Supplier*</label>
                        <select name="SupplierID" class="form-control <?php $__errorArgs = ['SupplierID'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Select Supplier</option>

                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($supplier->SupplierID); ?>"
                                <?php echo e(old('SupplierID') == $supplier->SupplierID ? 'selected' : ''); ?>>
                                <?php echo e($supplier->SupplierName); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <?php $__errorArgs = ['SupplierID'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="form-text text-danger"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="form-group">
                        <label>Brand*</label>
                        <input type="text" name="Brand" value="<?php echo e(old('Brand')); ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Description (optional)</label>
                        <textarea name="Description" class="form-control" rows="4"><?php echo e(old('Description')); ?></textarea>
                    </div>
                </div>
            </div>


            
            <div id="images" class="tab-content">
                <div class="form-row">
                    <?php for($i=1;$i<=5;$i++): ?>
                        <div class="upload-box" id="uploadBox-<?php echo e($i); ?>">
                        <div class="upload-placeholder" onclick="document.getElementById('image-<?php echo e($i); ?>').click()">
                            <i class="fas fa-cloud-upload-alt"></i> Image <?php echo e($i); ?>

                        </div>
                        <img id="preview-<?php echo e($i); ?>" src="<?php echo e(asset('image/default-pet.png')); ?>" style="display:none;">
                        <input type="file" name="ImageURL<?php echo e($i); ?>" id="image-<?php echo e($i); ?>"
                            accept="image/*" onchange="previewImage(this,'<?php echo e($i); ?>')" style="display: none;">
                        <button type="button" class="remove-image" onclick="removeImage('<?php echo e($i); ?>')" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                </div>
                <?php endfor; ?>
            </div>
            </div>

            
            <div id="variants" class="tab-content variants-section">
                <div id="variant-list">
                    <div class="variant-item" data-index="0">
                        <div class="variant-header">
                            <h4>Variant #1*</h4><button type="button" class="remove-variant" onclick="removeVariant(this)">Remove</button>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Variant Key*</label>
                                <input type="text" name="variants[0][VariantKey]" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Price (RM)*</label>
                                <input type="number" name="variants[0][Price]" class="form-control" step="0.01" value="0">
                            </div>
                        </div>

                        
                        <div class="outlet-stock-section">
                            <h5>Outlet Stock</h5>
                            <div class="outlet-stock-list">
                                <div class="outlet-stock-item" data-index="0">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Outlet*</label>
                                            <select name="variants[0][outlets][0][OutletID]" class="form-control">
                                                <option value="">Select Outlet</option>
                                                <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($outlet->OutletID); ?>"><?php echo e($outlet->State); ?> -- <?php echo e($outlet->City); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Stock Quantity*</label>
                                            <input type="number" name="variants[0][outlets][0][StockQty]" class="form-control" min="0" value="0">
                                        </div>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger" onclick="removeOutletStock(this)">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="addOutletStock(this)">Add Outlet Stock</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success" onclick="addVariant()">Add Variant</button>
            </div>


            
            <div class="form-group">
                <button type="submit" class="btn btn-success">Save Accessory</button>
            </div>
        </form>

        
        <div id="outletStockTemplate" style="display:none;">
            <div class="outlet-stock-item" data-index="__INDEX__">
                <div class="form-row">
                    <div class="form-group">
                        <label>Outlet</label>
                        <select name="variants[__VARIANT__][outlets][__INDEX__][OutletID]" class="form-control">
                            <option value="">Select Outlet</option>
                            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($outlet->OutletID); ?>"><?php echo e($outlet->State); ?> -- <?php echo e($outlet->City); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="variants[__VARIANT__][outlets][__INDEX__][StockQty]" class="form-control" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-danger" onclick="removeOutletStock(this)">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('js/accessory.js')); ?>">
    </script>

    <script>
        // Your validation / form JS here
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');

            form.addEventListener('submit', function(e) {
                let basicValid = true;
                let variantValid = false;

                // Check required basic info fields
                const requiredFields = ['AccessoryName', 'Category', 'SupplierID', 'Brand'];
                requiredFields.forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (!field || field.value.trim() === '') {
                        basicValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Check if at least one variant exists
                const variants = document.querySelectorAll('#variant-list .variant-item');
                variants.forEach(variant => {
                    const outlets = variant.querySelectorAll('.outlet-stock-item');
                    if (outlets.length > 0) variantValid = true;
                });

                if (!basicValid) {
                    alert('Please fill all required basic info fields.');
                    e.preventDefault();
                    return false;
                }

                if (!variantValid) {
                    alert('Please add at least one variant with outlet stock.');
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>


    <?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/admin/accessory/accessoryAdd.blade.php ENDPATH**/ ?>