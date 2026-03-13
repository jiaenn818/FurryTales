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
    </style>

    <div class="admin-content">
        <div class="admin-header">
            <h1><i class="fas fa-ticket"></i> All Vouchers Management</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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