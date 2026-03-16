@extends('layouts.admin')
@section('title', 'All Voucher Management')

@section('content')
    <!-- Bootstrap 5 CSS (make sure this is not already in your layout) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--color-brand-light);
        }

        .admin-header h1 {
            font-family: var(--font-heading);
            color: var(--color-brand-dark);
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-header h1 i {
            color: var(--color-brand-primary-gradient-start);
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }


        .container {
            max-width: 1100px;
        }

        .table {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .table th {
            background: #f5f7fa;
            font-weight: 600;
        }

        .btn-primary {
            border-radius: 8px;
            padding: 8px 16px;
        }

        .btn-warning,
        .btn-danger {
            border-radius: 6px;
        }

        .modal-content {
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #4a6fa5, #6fa3ef);
            color: #fff;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-footer .btn {
            border-radius: 8px;
        }

        .expired-row {
            background-color: #f1f1f1 !important;
            color: #999 !important;
        }

        .expired-row td {
            text-decoration: line-through;
            opacity: 0.7;
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
    </style>

    <div class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-ticket"></i> All Vouchers Management</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.voucher.index') }}" class="filters-container">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by Voucher ID, Discount, etc.">

            <button type="submit">Search</button>
            <a href="{{ route('admin.voucher.index') }}">Clear</a>
        </form>

        <!-- Add Button -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
            + Add Voucher
        </button>

        <!-- Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Voucher ID</th>
                    <th>Discount</th>
                    <th>Min Spend</th>
                    <th>Expiry</th>
                    <th>Usage Limit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vouchers as $voucher)
                    @php
                        $isExpired = $voucher->expiryDate && \Carbon\Carbon::parse($voucher->expiryDate)->isPast();
                    @endphp

                    <tr class="{{ $isExpired ? 'expired-row' : '' }}">
                        <td>{{ $voucher->voucherID }}</td>
                        <td>RM {{ $voucher->discountAmount }}</td>
                        <td>RM {{ $voucher->minSpend }}</td>
                        <td>
                            {{ $voucher->expiryDate ? \Carbon\Carbon::parse($voucher->expiryDate)->format('Y-m-d') : 'No expiry' }}
                        </td>
                        <td>{{ $voucher->usageLimit ?? 'Unlimited' }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                onclick="openEditModal(
                            '{{ $voucher->voucherID }}',
                            '{{ $voucher->discountAmount }}',
                            '{{ $voucher->minSpend }}',
                            '{{ $voucher->expiryDate ? \Carbon\Carbon::parse($voucher->expiryDate)->format('Y-m-d') : '' }}',
                            '{{ $voucher->usageLimit }}'
                        )"
                                data-bs-toggle="modal" data-bs-target="#editVoucherModal">
                                Edit
                            </button>

                            <form action="{{ route('admin.voucher.destroy', $voucher->voucherID) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this voucher?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $vouchers->links() }}
        </div>
    </div>

    <!-- ================= ADD MODAL ================= -->
    <div class="modal fade" id="addVoucherModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.voucher.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label>Voucher ID</label>
                        <input type="text" name="voucherID" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Discount Amount (RM)</label>
                        <input type="number" step="0.01" name="discountAmount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Minimum Spend (RM)</label>
                        <input type="number" step="0.01" name="minSpend" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Expiry Date</label>
                        <input type="date" name="expiryDate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Usage Limit (leave empty = unlimited)</label>
                        <input type="number" name="usageLimit" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Create</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT MODAL ================= -->
    <div class="modal fade" id="editVoucherModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Voucher ID</label>
                        <input type="text" id="editVoucherID" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label>Discount Amount (RM)</label>
                        <input type="number" step="0.01" name="discountAmount" id="editDiscountAmount"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Minimum Spend (RM)</label>
                        <input type="number" step="0.01" name="minSpend" id="editMinSpend" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Expiry Date</label>
                        <input type="date" name="expiryDate" id="editExpiryDate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Usage Limit (leave empty = unlimited)</label>
                        <input type="number" name="usageLimit" id="editUsageLimit" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS (required for modal to work) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function openEditModal(id, discount, minSpend, expiry, usageLimit) {
            document.getElementById('editVoucherID').value = id;
            document.getElementById('editDiscountAmount').value = discount;
            document.getElementById('editMinSpend').value = minSpend;
            document.getElementById('editExpiryDate').value = expiry ?? '';
            document.getElementById('editUsageLimit').value = usageLimit;

            // Set form action dynamically
            document.getElementById('editForm').action = "{{ route('admin.voucher.update', ':id') }}".replace(':id', id);
        }
    </script>
@endsection
