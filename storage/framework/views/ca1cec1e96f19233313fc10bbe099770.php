

<?php $__env->startSection('title', 'Outlet Management'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/petList.css')); ?>">
    <style>
        :root {
            --font-heading: 'Candara', 'Instrument Sans', sans-serif;
            --font-body: 'Candara', 'Instrument Sans', sans-serif;
            --color-brand-dark: #5a2c2c;
            --color-brand-medium: #8F5D54;
            --color-brand-light: #D9CAC7;
            --color-brand-soft: #fff2f5;
            --color-brand-accent: #ffccd9;
            --color-brand-primary-gradient-start: #a95c68;
            --color-brand-primary-gradient-end: #d9999b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            color: var(--color-brand-dark);
            background: var(--color-brand-soft);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
            color: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(90, 44, 44, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-family: var(--font-heading);
            font-size: 28px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header h1 i {
            font-size: 32px;
            color: var(--color-brand-accent);
        }

        /* Buttons */
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
            color: white;
            box-shadow: 0 4px 15px rgba(169, 92, 104, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(169, 92, 104, 0.4);
        }

        .btn-secondary {
            background: var(--color-brand-light);
            color: var(--color-brand-dark);
        }

        .btn-success {
            background: #4caf50;
            color: white;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-warning {
            background: #ff9800;
            color: white;
        }

        .btn-info {
            background: #2196f3;
            color: white;
        }

        .btn-sm {
            padding: 8px 15px;
            font-size: 13px;
        }

        /* Cards */
        .cardOutlet {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(90, 44, 44, 0.08);
            border: 1px solid var(--color-brand-light);
            transition: transform 0.3s ease;
        }

        .cardOutlet:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(90, 44, 44, 0.12);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(90, 44, 44, 0.05);
            border: 1px solid var(--color-brand-light);
        }

        .stat-card h3 {
            color: var(--color-brand-primary-gradient-start);
            font-family: var(--font-heading);
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .stat-card p {
            color: var(--color-brand-medium);
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(90, 44, 44, 0.05);
            border: 1px solid var(--color-brand-light);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .table th {
            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
            color: white;
            padding: 18px 20px;
            text-align: left;
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table th:first-child {
            border-top-left-radius: 12px;
        }

        .table th:last-child {
            border-top-right-radius: 12px;
        }

        .table td {
            padding: 18px 20px;
            border-bottom: 1px solid var(--color-brand-light);
            color: var(--color-brand-dark);
            font-size: 15px;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover {
            background-color: var(--color-brand-soft);
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            font-family: var(--font-heading);
            display: inline-block;
        }

        .badge-primary {
            background: var(--color-brand-light);
            color: var(--color-brand-dark);
        }

        .badge-success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-info {
            background: #e3f2fd;
            color: #1565c0;
        }

        /* Action Buttons Container */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(90, 44, 44, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            padding: 30px;
            box-shadow: 0 20px 50px rgba(90, 44, 44, 0.2);
            animation: modalSlideIn 0.3s ease;
            border: 1px solid var(--color-brand-light);
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--color-brand-light);
        }

        .modal-header h3 {
            color: var(--color-brand-dark);
            font-family: var(--font-heading);
            font-size: 24px;
            font-weight: 600;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: var(--color-brand-medium);
            transition: color 0.3s;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-modal:hover {
            color: var(--color-brand-dark);
            background: var(--color-brand-soft);
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--color-brand-dark);
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 15px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--color-brand-light);
            border-radius: 10px;
            font-size: 15px;
            font-family: var(--font-body);
            color: var(--color-brand-dark);
            background: white;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--color-brand-primary-gradient-start);
            box-shadow: 0 0 0 3px rgba(169, 92, 104, 0.1);
        }

        .form-control:read-only {
            background-color: var(--color-brand-soft);
            cursor: not-allowed;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238F5D54' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 20px;
            padding-right: 45px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid var(--color-brand-light);
        }

        /* Messages/Alerts */
        .alert {
            padding: 18px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid transparent;
            font-family: var(--font-heading);
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-color: #c8e6c9;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-color: #ffcdd2;
        }

        .alert-warning {
            background: #fff3e0;
            color: #ef6c00;
            border-color: #ffe0b2;
        }

        .close-alert {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
            transition: opacity 0.3s;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-alert:hover {
            opacity: 1;
            background: rgba(0, 0, 0, 0.05);
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 30px;
            color: var(--color-brand-primary-gradient-start);
            display: none;
        }

        .loading.active {
            display: block;
        }

        .loading i {
            font-size: 24px;
            margin-right: 10px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--color-brand-medium);
        }

        .empty-state i {
            font-size: 60px;
            color: var(--color-brand-light);
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-state p {
            font-size: 18px;
            font-family: var(--font-heading);
        }

        .bubble-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .bubble {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .pet-bubble {
            background: var(--color-brand-light);
            color: var(--color-brand-dark);
        }

        .stock-bubble {
            background: #8FBC8F;
            color: white;
        }

        .form-control:disabled {
            background-color: var(--color-brand-soft);
            color: var(--color-brand-medium);
            cursor: not-allowed;
            opacity: 1;
            /* prevent browser dimming */
        }

        /* Enabled inputs & dropdowns */
        .form-control:not(:disabled),
        select.form-control {
            background-color: #ffffff;
            cursor: text;
        }

        /* Dropdown cursor fix */
        select.form-control {
            cursor: pointer;
        }

        /* Focus only for enabled fields */
        .form-control:focus:not(:disabled),
        select.form-control:focus {
            border-color: var(--color-brand-primary-gradient-start);
            box-shadow: 0 0 0 3px rgba(169, 92, 104, 0.12);
        }

        .form-control:disabled {
            font-weight: 600;
            letter-spacing: 0.3px;
        }



        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table th,
            .table td {
                padding: 12px 15px;
                font-size: 14px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .modal-content {
                width: 95%;
                padding: 20px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

        }


        /* Print Styles */
        @media print {

            .header .btn,
            .action-buttons,
            .modal-overlay,
            .back-btn {
                display: none !important;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ccc;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-store"></i>
                Outlet Management
            </h1>
            <button class="btn btn-primary" onclick="openModal('add')">
                <i class="fas fa-plus"></i> Add New Outlet
            </button>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo e($outlets->count()); ?></h3>
                <p>Total Outlets</p>
            </div>
            <div class="stat-card">
                <h3><?php echo e($statesCount); ?></h3>
                <p>States Covered</p>
            </div>
            <div class="stat-card">
                <h3><?php echo e(number_format($averagePets, 1)); ?></h3>
                <p>Avg Pets per Outlet</p>
            </div>
        </div>

        <!-- Messages -->
        <?php if(session('success')): ?>
            <div class="alert alert-success" id="successMessage">
                <div>
                    <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                </div>
                <button class="close-alert" onclick="this.parentElement.style.display='none'">
                    &times;
                </button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-error" id="errorMessage">
                <div>
                    <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                </div>
                <button class="close-alert" onclick="this.parentElement.style.display='none'">
                    &times;
                </button>
            </div>
        <?php endif; ?>

        <?php if(session('warning')): ?>
            <div class="alert alert-warning" id="warningMessage">
                <div>
                    <i class="fas fa-exclamation-triangle"></i> <?php echo e(session('warning')); ?>

                </div>
                <button class="close-alert" onclick="this.parentElement.style.display='none'">
                    &times;
                </button>
            </div>
        <?php endif; ?>

        <!-- Loading -->
        <div class="loading" id="loading">
            <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>

        <!-- Outlets Table -->
        <div class="cardOutlet">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Outlet ID</th>
                            <th>State</th>
                            <th>Location</th>
                            <th>Pets Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($outlet->OutletID); ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-primary"><?php echo e($outlet->State); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo e($outlet->City); ?></strong><br />
                                    <?php echo e($outlet->Postcode); ?>

                                </td>
                                <td>
                                    <span class="badge badge-success"><?php echo e($outlet->pets_count ?? 0); ?> pets</span>
                                    <span class="badge badge-info"><?php echo e($outlet->accessories_count ?? 0); ?> accessories</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning"
                                            onclick="openModal('edit', '<?php echo e($outlet->OutletID); ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <!-- CHANGE THIS LINE: Call confirmDelete with the outlet ID -->
                                        <button class="btn btn-sm btn-danger"
                                            onclick="confirmDelete('<?php echo e($outlet->OutletID); ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        <button class="btn btn-sm btn-info"
                                            onclick="viewOutlet('<?php echo e($outlet->OutletID); ?>')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-store-slash"></i>
                                        <p>No outlets found. Add your first outlet!</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" id="outletModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Outlet</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="outletForm" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="form-group">
                    <label for="OutletID"><i class="fas fa-id-badge"></i> Outlet ID</label>
                    <input type="text" id="OutletID" class="form-control" value="<?php echo e($nextOutletID); ?>" disabled>
                    <input type="hidden" name="OutletID" value="<?php echo e($nextOutletID); ?>">
                </div>
                <div class="form-group">
                    <label for="State"><i class="fas fa-map-marker-alt"></i> State *</label>
                    <select id="State" name="State" class="form-control" required>
                        <option value="">Select State</option>
                        <?php $__currentLoopData = ['Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang', 'Perak', 'Perlis', 'Penang', 'Selangor', 'Terengganu', 'Kuala Lumpur', 'Labuan', 'Putrajaya']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($state); ?>"><?php echo e($state); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="City"><i class="fas fa-city"></i> City *</label>
                    <input type="text" id="City" name="City" class="form-control" required
                        placeholder="e.g., Setapak">
                </div>
                <div class="form-group">
                    <label for="AddressLine1"><i class="fas fa-home"></i> Address Line 1 *</label>
                    <input type="text" id="AddressLine1" name="AddressLine1" class="form-control" required
                        placeholder="e.g., 123, Jalan Example">
                </div>
                <div class="form-group">
                    <label for="PostCode"><i class="fas fa-mail-bulk"></i> Post Code *</label>
                    <input type="text" id="PostCode" name="PostCode" class="form-control" required
                        pattern="[0-9]{5}" title="5-digit post code required" placeholder="e.g., 50000">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> <span id="submitText">Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
                <button class="close-modal" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div style="padding: 20px;">
                <p>Are you sure you want to delete outlet <strong id="deleteOutletId"></strong>?</p>
                <p style="color: #f44336; font-weight: 600; margin-top: 10px;">
                    <i class="fas fa-exclamation-circle"></i> This action cannot be undone!
                </p>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <!-- CHANGE THIS: Call performDelete() instead of deleteOutlet() -->
                    <button type="button" class="btn btn-danger" onclick="performDelete()">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal-overlay" id="viewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-store"></i> Outlet Details</h3>
                <button class="close-modal" onclick="closeViewModal()">&times;</button>
            </div>
            <div style="padding: 20px;">
                <div class="form-group">
                    <label>Outlet ID</label>
                    <div class="form-control" style="background: var(--color-brand-soft);">
                        <strong id="viewOutletId"></strong>
                    </div>
                </div>
                <div class="form-group">
                    <label>State</label>
                    <div class="form-control" style="background: var(--color-brand-soft);">
                        <strong id="viewState"></strong>
                    </div>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <div class="form-control" style="background: var(--color-brand-soft);">
                        <strong id="viewCity"></strong>
                    </div>
                </div>
                <div class="form-group">
                    <label>Address Line 1</label>
                    <div class="form-control" style="background: var(--color-brand-soft);">
                        <strong id="viewAddressLine1"></strong>
                    </div>
                </div>
                <div class="form-group">
                    <label>Post Code</label>
                    <div class="form-control" style="background: var(--color-brand-soft);">
                        <strong id="viewPostCode"></strong>
                    </div>
                </div>
                <div class="form-group">
                    <label>Associated Pets</label>
                    <div class="form-control" style="background: var(--color-brand-soft);">
                        <strong id="viewPetsCount"></strong> pets
                    </div>
                </div>
                <div class="form-group">
                    <label>Pets Available</label>
                    <div id="viewPetsList"></div>
                </div>

                <div class="form-group">
                    <label>Accessories Stock</label>
                    <div id="viewAccessoriesList"></div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeViewModal()">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function closeModal() {
            document.getElementById('outletModal').classList.remove('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            outletToDelete = '';
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.remove('active');
        }


        function viewOutlet(outletId) {
            fetch(`/admin/outlets/${outletId}/edit`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('viewOutletId').textContent = data.OutletID;
                    document.getElementById('viewState').textContent = data.State;
                    document.getElementById('viewCity').textContent = data.City;
                    document.getElementById('viewAddressLine1').textContent = data.AddressLine1;
                    document.getElementById('viewPostCode').textContent = data.Postcode;
                    document.getElementById('viewPetsCount').textContent = data.pets.length;
                    // document.getElementById('viewAccessoryCount').textContent = data.outletAccessories.length;

                    /* PETS */
                    const petsList = document.getElementById('viewPetsList');
                    petsList.innerHTML = data.pets.length ?
                        `<div class="bubble-container">` +
                        data.pets.map(p =>
                            `<span class="bubble pet-bubble">${p.PetName} (${p.Type})</span>`
                        ).join('') +
                        `</div>` :
                        "<p>No pets available</p>";

                    /* ACCESSORIES */
                    const accList = document.getElementById('viewAccessoriesList');

                    const accessories = Array.isArray(data.outlet_accessories) ?
                        data.outlet_accessories :
                        [];

                    if (!accessories.length) {
                        accList.innerHTML = "<p>No accessories stock</p>";
                    } else {

                        // 1️⃣ Group variants by AccessoryName
                        const grouped = {};

                        accessories.forEach(item => {
                            const name = item.variant.accessory.AccessoryName;

                            if (!grouped[name]) {
                                grouped[name] = [];
                            }

                            grouped[name].push({
                                variant: item.variant.VariantKey,
                                qty: item.StockQty
                            });
                        });

                        // 2️⃣ Render grouped result
                        accList.innerHTML = Object.entries(grouped).map(([name, variants]) => `
        <div style="margin-bottom:16px;">
            <strong>${name}</strong>
            <div class="bubble-container" style="margin-top:6px;">
                ${variants.map(v => `
                        <span class="bubble stock-bubble">
                            ${v.variant} — Qty: ${v.qty}
                        </span>
                    `).join('')}
            </div>
        </div>
    `).join('');
                    }

                    document.getElementById('viewModal').classList.add('active');
                });
        }


        let currentOutletId = '';

        function openModal(type, outletId = '') {
            const modal = document.getElementById('outletModal');
            const form = document.getElementById('outletForm');
            const submitText = document.getElementById('submitText');

            currentOutletId = outletId;

            if (type === 'add') {
                document.getElementById('OutletID').readOnly = false;
                form.action = "<?php echo e(route('admin.outlets.store')); ?>"; 
                document.getElementById('formMethod').value = 'POST';
                submitText.textContent = 'Add Outlet';
                form.reset();
            } else if (type === 'edit') {
                document.getElementById('OutletID').readOnly = true;
                submitText.textContent = 'Update Outlet';
                document.getElementById('formMethod').value = 'PUT';
                form.action = `/admin/outlets/${outletId}`; 
                    fetch(`/admin/outlets/${outletId}/edit`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('OutletID').value = data.OutletID;
                        document.getElementById('State').value = data.State;
                        document.getElementById('City').value = data.City;
                        document.getElementById('AddressLine1').value = data.AddressLine1;
                        document.getElementById('PostCode').value = data.Postcode;
                    })
                    .catch(err => alert('Error loading outlet data'));
            }

            modal.classList.add('active');
        }

        let outletToDelete = ''; 


        // Open confirmation modal
        function confirmDelete(outletId) {
            outletToDelete = outletId;
            document.getElementById('deleteOutletId').textContent = outletId;
            document.getElementById('deleteModal').classList.add('active');
        }

        // Perform the actual deletion
        function performDelete() {
            if (!outletToDelete) {
                alert('No outlet selected for deletion');
                return;
            }

            // Show loading
            document.getElementById('loading').classList.add('active');

            // Send DELETE request
                fetch(`/admin/outlets/${outletToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch {
                            return {
                                success: true,
                                message: text
                            };
                        }
                    });
                })
                .then(data => {
                    document.getElementById('loading').classList.remove('active');

                    if (data.success || data.message) {
                        // Show success message
                        showAlert('success', data.message || 'Outlet deleted successfully!');

                        // Close modal
                        closeDeleteModal();

                        // Reload page after 1 second
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showAlert('error', data.error || 'Failed to delete outlet.');
                        closeDeleteModal();
                    }
                })
                .catch(error => {
                    document.getElementById('loading').classList.remove('active');
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred while deleting the outlet.');
                    closeDeleteModal();
                });
        }

        // Helper function to show alerts
        function showAlert(type, message) {
            // Remove any existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.innerHTML = `
        <div>
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> 
            ${message}
        </div>
        <button class="close-alert" onclick="this.parentElement.style.display='none'">
            &times;
        </button>
    `;

            // Insert after header
            const header = document.querySelector('.header');
            header.parentNode.insertBefore(alertDiv, header.nextSibling);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.style.opacity = '0';
                    setTimeout(() => alertDiv.remove(), 300);
                }
            }, 5000);
        }
    </script>
<?php $__env->stopPush(); ?>

</body>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\XAMPP\htdocs\finalyear\resources\views/admin/outlet.blade.php ENDPATH**/ ?>