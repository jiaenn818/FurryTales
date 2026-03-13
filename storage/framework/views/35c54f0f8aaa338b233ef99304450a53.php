

<?php $__env->startSection('title', 'Supplier Management'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        :root {
            --font-heading: 'Candara', sans-serif;
            --font-body: 'Candara', sans-serif;

            --brand-dark: #5a2c2c;
            --brand-medium: #8F5D54;
            --brand-light: #D9CAC7;
            --brand-soft: #fff2f5;

            --primary: #d9999b;
            --primary-dark: #a95c68;

            --success: #8FBC8F;
            --danger: #D18D7A;
            --warning: #E8C07D;

            --radius: 12px;
            --shadow: 0 4px 20px rgba(90, 44, 44, 0.08);
            --transition: all 0.3s ease;
        }

        /* GENERAL */
        body {
            font-family: var(--font-body);
        }

        h1 {
            font-family: var(--font-heading);
            color: var(--brand-dark);
        }

        /* BUTTON SYSTEM */
        .btn {
            border: none;
            padding: 0.7rem 1.4rem;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-view {
            background: #7aa6d1;
            color: white;
        }

        .btn-add {
            background: linear-gradient(135deg, var(--success), #7CA67C);
            color: white;
        }

        .btn-edit {
            background: var(--warning);
            color: var(--brand-dark);
        }

        .btn-delete {
            background: var(--danger);
            color: white;
        }

        /* TABLE */
        .table-container {
            overflow-x: auto;
            margin: 2rem 0;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }

        thead th {
            color: white;
            padding: 1rem;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        tbody td {
            padding: 1rem;
        }

        tbody tr:hover {
            background: var(--brand-soft);
        }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(90, 44, 44, 0.7);
            backdrop-filter: blur(6px);
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .modal-content input {
            width: 100%;
            padding: 0.7rem;
            margin-top: 0.5rem;
            border-radius: var(--radius);
            border: 1px solid var(--brand-light);
        }

        /* ALERTS */
        .alert-success {
            background: rgba(143, 188, 143, .1);
            border-left: 4px solid var(--success);
            padding: 1rem;
            border-radius: var(--radius);
        }

        .alert-error {
            background: rgba(209, 141, 122, .1);
            border-left: 4px solid var(--danger);
            padding: 1rem;
            border-radius: var(--radius);
        }

        /* VIEW MODAL ENHANCEMENTS */
        .modal-content h4 {
            margin-top: 1.2rem;
            color: var(--brand-dark);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 120px auto;
            gap: 0.5rem 1rem;
            margin-bottom: 1rem;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: var(--primary);
            color: white;
        }

        .list-card {
            background: var(--brand-soft);
            border-radius: var(--radius);
            padding: 0.7rem 1rem;
            margin-bottom: 0.6rem;
        }

        .variant-tag {
            background: var(--warning);
            color: var(--brand-dark);
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 5px;
        }

        .outlet-tag {
            background: var(--success);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 5px;
        }

        .variant-tag,
        .outlet-tag {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            max-width: 100%;
        }


        .modal-content {
            max-height: 80vh;
            overflow-y: auto;
        }

        .bubble-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 6px;
        }

        .bubble {
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }

        .variant-row {
            margin-top: 6px;
        }

        .variant-tag,
        .outlet-tag {
            white-space: nowrap;
            flex-shrink: 0;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>

    <h1>Supplier Management</h1>

    
    <?php if(session('success')): ?>
        <div class="alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <?php if($errors->any()): ?>
        <div class="alert-error">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <button class="btn btn-add" onclick="openAddModal()">
        <span class="icon"> + </span>
        <span>Add Supplier</span>
    </button>

    <div class="table-container">
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($s->SupplierID); ?></td>
                        <td>
                            <?php echo e($s->SupplierName); ?>

                            <small style="color:#888;">
                                <?php echo e($s->SupplierAddress ? ' - ' . $s->SupplierAddress : ''); ?>

                            </small>
                        </td>
                        <td><?php echo e($s->SupplierEmail); ?></td>
                        <td><?php echo e($s->SupplierPhoneNumber); ?></td>
                        <td>
                            <div class="action-buttons">

                                <button class="btn btn-view"
                                    onclick='openViewModal(
    <?php echo json_encode($s->SupplierID, 15, 512) ?>,
    <?php echo json_encode($s->SupplierName, 15, 512) ?>,
    <?php echo json_encode($s->SupplierEmail, 15, 512) ?>,
    <?php echo json_encode($s->SupplierPhoneNumber, 15, 512) ?>,
    <?php echo json_encode($s->SupplierAddress, 15, 512) ?>,
    <?php echo json_encode($s->pets, 15, 512) ?>,
    <?php echo json_encode($s->accessories, 15, 512) ?>
)'>
                                    👁 View
                                </button>

                                <button class="btn btn-edit"
                                    onclick="openEditModal(
                                    '<?php echo e($s->SupplierID); ?>',
                                    '<?php echo e($s->SupplierName); ?>',
                                    '<?php echo e($s->SupplierEmail); ?>',
                                    '<?php echo e($s->SupplierPhoneNumber); ?>',
                                    '<?php echo e($s->SupplierAddress); ?>'
                                )">
                                    ✏️ Edit
                                </button>

                                <form action="<?php echo e(route('admin.suppliers.destroy', $s->SupplierID)); ?>" method="POST"
                                    style="display:inline" onsubmit="return confirm('Delete this supplier?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-delete">
                                        🗑 Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5">No suppliers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div class="modal-overlay" id="viewModal">
        <div class="modal-content">
            <h3>Supplier Details</h3>

            <div class="detail-grid">
                <strong>ID</strong><span id="viewID"></span>
                <strong>Name</strong><span id="viewName"></span>
                <strong>Email</strong><span id="viewEmail"></span>
                <strong>Phone</strong><span id="viewPhone"></span>
                <strong>Address</strong><span id="viewAddress"></span>
            </div>

            <hr>

            <h4>🐾 Pets Supplied</h4>
            <div id="petList"></div>

            <h4>🎒 Accessories Supplied</h4>
            <div id="accessoryList"></div>

            <div class="modal-actions">
                <button onclick="closeModals()">Close</button>
            </div>
        </div>
    </div>

    
    <div class="modal-overlay" id="addModal">
        <div class="modal-content">
            <h3>Add Supplier</h3>

            <form method="POST" action="<?php echo e(route('admin.suppliers.store')); ?>">
                <?php echo csrf_field(); ?>

                <label>ID</label>
                <input type="text" name="SupplierID" value="<?php echo e($nextSupplierID); ?>" readonly>

                <label>Name</label>
                <input type="text" name="SupplierName" required>

                <label>Email</label>
                <input type="email" name="SupplierEmail" required>

                <label>Phone</label>
                <input type="text" name="SupplierPhoneNumber" required>

                <label>Address</label>
                <input type="text" name="SupplierAddress" required>

                <div class="modal-actions">
                    <button type="submit">✔ Save</button>
                    <button type="button" onclick="closeModals()">✖ Cancel</button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <h3>Edit Supplier</h3>

            <form method="POST" id="editForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <label>ID</label>
                <input type="text" name="SupplierID" id="editID" readonly>

                <label>Name</label>
                <input type="text" name="SupplierName" id="editName" required>

                <label>Email</label>
                <input type="email" name="SupplierEmail" id="editEmail" required>

                <label>Phone</label>
                <input type="text" name="SupplierPhoneNumber" id="editPhone" required>

                <label>Address</label>
                <input type="text" name="SupplierAddress" id="editAddress" required>

                <div class="modal-actions">
                    <button type="submit">Update</button>
                    <button type="button" onclick="closeModals()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // ===============================
        // VIEW MODAL
        // ===============================
        function openViewModal(id, name, email, phone, address, pets, accessories) {

            // Basic Info
            document.getElementById('viewID').innerText = id ?? '-';
            document.getElementById('viewName').innerText = name ?? '-';
            document.getElementById('viewEmail').innerText = email ?? '-';
            document.getElementById('viewPhone').innerText = phone ?? '-';
            document.getElementById('viewAddress').innerText = address ?? '-';

            // ===============================
            // PET LIST
            // ===============================
            const petList = document.getElementById('petList');

            if (!pets || pets.length === 0) {
                petList.innerHTML = "<p>No pets supplied</p>";
            } else {
                petList.innerHTML = pets.map(p => `
                <div class="list-card">
                    <strong>${p.PetName ?? '-'}</strong>
                    <span class="badge">${p.Type ?? '-'}</span>
                    <div>Breed: ${p.Breed ?? '-'}</div>
                </div>
            `).join('');
            }

            // ===============================
            // ACCESSORY LIST
            // ===============================
            const accessoryList = document.getElementById('accessoryList');

            if (!accessories || accessories.length === 0) {
                accessoryList.innerHTML = "<p>No accessories supplied</p>";
            } else {
                accessoryList.innerHTML = accessories.map(a => {

                    let variantsHTML = '';

                    if (a.variants && a.variants.length) {
                        variantsHTML = a.variants.map(v => {

                            let outletHTML = '';

                            if (v.outlet_accessories && v.outlet_accessories.length) {
                                outletHTML = v.outlet_accessories.map(o => `
                                <span class="bubble outlet-tag">
                                    ${o.outlet?.State ?? '-'} (${o.StockQty ?? 0})
                                </span>
                            `).join('');
                            }

                            return `
                            <div class="bubble-container variant-row">
                                <span class="bubble variant-tag">
                                    ${v.VariantKey ?? '-'}
                                </span>
                                ${outletHTML}
                            </div>
                        `;
                        }).join('');
                    }

                    return `
                    <div class="list-card">
                        <strong>${a.AccessoryName ?? '-'}</strong>
                        <div>Category: ${a.Category ?? '-'}</div>
                        ${variantsHTML}
                    </div>
                `;
                }).join('');
            }

            // SHOW MODAL
            document.getElementById('viewModal').classList.add('active');
        }


        // ===============================
        // ADD MODAL
        // ===============================
        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }


        // ===============================
        // EDIT MODAL
        // ===============================
        function openEditModal(id, name, email, phone, address) {
            document.getElementById('editID').value = id ?? '';
            document.getElementById('editName').value = name ?? '';
            document.getElementById('editEmail').value = email ?? '';
            document.getElementById('editPhone').value = phone ?? '';
            document.getElementById('editAddress').value = address ?? '';

            // IMPORTANT: correct route
            document.getElementById('editForm').action = '/admin/suppliers/' + id;

            document.getElementById('editModal').classList.add('active');
        }


        // ===============================
        // CLOSE ALL MODALS
        // ===============================
        function closeModals() {
            document.getElementById('viewModal').classList.remove('active');
            document.getElementById('addModal').classList.remove('active');
            document.getElementById('editModal').classList.remove('active');
        }


        // ===============================
        // OPTIONAL: CLICK OUTSIDE TO CLOSE
        // ===============================
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/admin/supplier.blade.php ENDPATH**/ ?>