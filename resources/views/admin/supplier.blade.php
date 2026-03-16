@extends('layouts.admin')

@section('title', 'Supplier Management')

@push('styles')
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

        /* =========================
           PAGINATION
        ========================= */
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding-left: 0;
            margin: 30px;
            gap: 5px;
        }

        .pagination li a,
        .pagination li span {
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid var(--color-brand-medium);
            text-decoration: none;
            color: var(--color-brand-dark);
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .pagination li a:hover {
            background-color: var(--color-brand-light);
        }

        .pagination li.active span {
            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
            color: #fff;
            border-color: transparent;
        }

        .pagination li.disabled span {
            color: #aaa;
            cursor: not-allowed;
            background-color: #f8f8f8;
            border-color: #ddd;
        }

        .filters-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin: 16px 0 24px;
            padding: 12px 16px;
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .filters-container input[type="text"],
        .filters-container select {
            flex: 1 1 240px;
            min-width: 180px;
            padding: 10px 14px;
            border: 1px solid rgba(0, 0, 0, 0.24);
            border-radius: 30px;
            font-size: 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #fff;
        }

        .filters-container input[type="text"]:focus,
        .filters-container select:focus {
            outline: none;
            border-color: rgba(48, 133, 214, 0.8);
            box-shadow: 0 0 0 3px rgba(48, 133, 214, 0.18);
        }

        .filters-container button[type="submit"],
        .filters-container a {
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 999px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .filters-container button[type="submit"] {
            border: none;
            background: linear-gradient(135deg, #2a9df4, #1b6dd5);
            color: #fff;
        }

        .filters-container button[type="submit"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(26, 101, 183, 0.25);
        }

        .filters-container a {
            border: 1px solid rgba(0, 0, 0, 0.16);
            background: rgba(255, 255, 255, 0.85);
            color: rgba(0, 0, 0, 0.75);
        }

        .filters-container a:hover {
            background: rgba(0, 0, 0, 0.06);
        }
    </style>
@endpush
@section('content')

    <h1>Supplier Management</h1>

    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.suppliers.index') }}" class="filters-container">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by Supplier Name, email, ID">
        
        <button type="submit">Search</button>
        <a href="{{ route('admin.suppliers.index') }}">Clear</a>
    </form>

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
                @forelse($suppliers as $s)
                    <tr>
                        <td>{{ $s->SupplierID }}</td>
                        <td>
                            {{ $s->SupplierName }}
                            <small style="color:#888;">
                                {{ $s->SupplierAddress ? ' - ' . $s->SupplierAddress : '' }}
                            </small>
                        </td>
                        <td>{{ $s->SupplierEmail }}</td>
                        <td>{{ $s->SupplierPhoneNumber }}</td>
                        <td>
                            <div class="action-buttons">

                                <button class="btn btn-view"
                                    onclick='openViewModal(
                                    @json($s->SupplierID),
                                    @json($s->SupplierName),
                                    @json($s->SupplierEmail),
                                    @json($s->SupplierPhoneNumber),
                                    @json($s->SupplierAddress),
                                    @json($s->pets),
                                    @json($s->accessories)
                                )'>
                                    👁 View
                                </button>

                                <button class="btn btn-edit"
                                    onclick="openEditModal(
                                    '{{ $s->SupplierID }}',
                                    '{{ $s->SupplierName }}',
                                    '{{ $s->SupplierEmail }}',
                                    '{{ $s->SupplierPhoneNumber }}',
                                    '{{ $s->SupplierAddress }}'
                                )">
                                    ✏️ Edit
                                </button>

                                <form action="{{ route('admin.suppliers.destroy', $s->SupplierID) }}" method="POST"
                                    style="display:inline" onsubmit="return confirm('Delete this supplier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete">
                                        🗑 Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No suppliers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $suppliers->links() }}
        </div>

    </div>

    {{-- VIEW MODAL --}}
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

    {{-- ADD MODAL --}}
    <div class="modal-overlay" id="addModal">
        <div class="modal-content">
            <h3>Add Supplier</h3>

            <form method="POST" action="{{ route('admin.suppliers.store') }}">
                @csrf

                <label>ID</label>
                <input type="text" name="SupplierID" value="{{ $nextSupplierID }}" readonly>

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

    {{-- EDIT MODAL --}}
    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <h3>Edit Supplier</h3>

            <form method="POST" id="editForm">
                @csrf
                @method('PUT')

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

@endsection

@push('scripts')
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
@endpush
