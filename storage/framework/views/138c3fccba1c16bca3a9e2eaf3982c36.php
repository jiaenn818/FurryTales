

<?php $__env->startSection('title', 'Edit Accessory'); ?>

<?php $__env->startPush('styles'); ?>

<?php $__env->stopPush(); ?>


<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('admin.accessories.update', $accessory->AccessoryID)); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    
    <div class="form-tabs">
        <button type="button" class="tab-btn active" data-tab="basic">Basic Info</button>
        <button type="button" class="tab-btn" data-tab="images">Images</button>
        <button type="button" class="tab-btn" data-tab="variants">Variants</button>
    </div>

    
    <div id="basic" class="tab-content active">
        <div class="form-row">
            <div class="form-group">
                <label>Accessory ID</label>
                <input type="text" name="AccessoryID" value="<?php echo e(old('AccessoryID', $accessory->AccessoryID)); ?>" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label>Accessory Name*</label>
                <input type="text" name="AccessoryName" value="<?php echo e(old('AccessoryName', $accessory->AccessoryName)); ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Category*</label>
                <select name="Category" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php $__currentLoopData = $categoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat); ?>"
                        <?php echo e(old('Category', $accessory->Category) == $cat ? 'selected' : ''); ?>>
                        <?php echo e($cat); ?>

                    </option>
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
                        <?php echo e(old('SupplierID', $accessory->SupplierID) == $supplier->SupplierID ? 'selected' : ''); ?>>
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
                <input type="text" name="Brand" value="<?php echo e(old('Brand', $accessory->Brand)); ?>" class="form-control">
            </div>
            <div class="form-group">
                <label>Description (optional)</label>
                <textarea name="Description" class="form-control" rows="4"><?php echo e(old('Description', $accessory->Description)); ?></textarea>
            </div>
        </div>
    </div>
    
    <div id="images" class="tab-content">
        <div class="form-row">
            <?php for($i=1; $i<=5; $i++): ?>
                <div class="upload-box" id="uploadBox-<?php echo e($i); ?>">

                
                <div class="upload-placeholder" onclick="document.getElementById('image-<?php echo e($i); ?>').click()">
                    <i class="fas fa-cloud-upload-alt"></i> Image <?php echo e($i); ?>

                </div>

                
                <img id="preview-<?php echo e($i); ?>"
                    src="<?php echo e($accessory->{'ImageURL'.$i} ? asset($accessory->{'ImageURL'.$i}) : ''); ?>"
                    class="<?php echo e($accessory->{'ImageURL'.$i} ? 'd-block' : 'd-none'); ?>">

                
                <input type="file" name="ImageURL<?php echo e($i); ?>" id="image-<?php echo e($i); ?>"
                    accept="image/*" onchange="previewImage(this,'<?php echo e($i); ?>')" style="display: none;">

                
                <input type="hidden" name="removeImage<?php echo e($i); ?>" id="removeImage<?php echo e($i); ?>" value="0">

                
                <button type="button" class="remove-image <?php echo e($accessory->{'ImageURL'.$i} ? 'd-flex' : 'd-none'); ?>"
                    onclick="removeImage('<?php echo e($i); ?>')">
                    <i class="fas fa-times"></i>
                </button>

        </div>
        <?php endfor; ?>
    </div>
    </div>

    
    <div id="variants" class="tab-content variants-section">
        <div id="variant-list">
            <?php $__currentLoopData = $accessory->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vIndex => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="variant-item" data-index="<?php echo e($vIndex); ?>">
                <div class="variant-header">
                    <h4>Variant #<?php echo e($vIndex + 1); ?></h4>
                    <button type="button" class="remove-variant" onclick="removeVariant(this)">Remove</button>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Variant Key</label>
                        <input type="text" name="variants[<?php echo e($vIndex); ?>][VariantKey]" class="form-control"
                            value="<?php echo e(old("variants.$vIndex.VariantKey", $variant->VariantKey)); ?>">
                    </div>
                    <div class="form-group">
                        <label>Price (RM)</label>
                        <input type="number" name="variants[<?php echo e($vIndex); ?>][Price]" class="form-control" step="0.01"
                            value="<?php echo e(old("variants.$vIndex.Price", $variant->Price)); ?>">
                    </div>
                </div>

                
                <div class="outlet-stock-section">
                    <h5>Outlet Stock</h5>
                    <div class="outlet-stock-list">
                        <?php
                        $outletStocks = $accessory->findOutletStockByVariantID($variant->VariantID);
                        ?>

                        <?php if($outletStocks && count($outletStocks) > 0): ?>
                        <?php $__currentLoopData = $outletStocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oIndex => $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="outlet-stock-item" data-index="<?php echo e($oIndex); ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Outlet</label>
                                    <select name="variants[<?php echo e($vIndex); ?>][outlets][<?php echo e($oIndex); ?>][OutletID]" class="form-control">
                                        <option value="">Select Outlet</option>
                                        <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($outlet->OutletID); ?>"
                                            <?php echo e($stock['OutletID'] == $outlet->OutletID ? 'selected' : ''); ?>>
                                            <?php echo e($outlet['State']); ?> -- <?php echo e($outlet['City']); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Stock Quantity</label>
                                    <input type="number" name="variants[<?php echo e($vIndex); ?>][outlets][<?php echo e($oIndex); ?>][StockQty]"
                                        class="form-control" min="0" value="<?php echo e($stock['StockQty']); ?>">
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger" onclick="removeOutletStock(this)">Remove</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        
                        <div class="outlet-stock-item" data-index="0">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Outlet</label>
                                    <select name="variants[<?php echo e($vIndex); ?>][outlets][0][OutletID]" class="form-control">
                                        <option value="">Select Outlet</option>
                                        <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($outlet->OutletID); ?>"><?php echo e($outlet->State); ?> -- <?php echo e($outlet->City); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Stock Quantity</label>
                                    <input type="number" name="variants[<?php echo e($vIndex); ?>][outlets][0][StockQty]" class="form-control" min="0" value="0">
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger" onclick="removeOutletStock(this)">Remove</button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="addOutletStock(this)">Add Outlet Stock</button>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <button type="button" class="btn btn-success" onclick="addVariant()">Add Variant</button>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary mt-3">Update Accessory</button>
    </div>
</form>


<script type="text/html" id="outletStockTemplate">
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
</script>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/accessory.js')); ?>"></script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/admin/accessory/accessoryEdit.blade.php ENDPATH**/ ?>