@extends('layouts.admin')

@section('title', 'View All Users')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/petList.css') }}">
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

        /* Reset */
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
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
            color: white;
            font-weight: 600;
            transition: 0.3s;
        }

        .tab.active {
            background: var(--color-brand-accent);
            color: var(--color-brand-dark);
            font-weight: 700;
        }

        /* Table container */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #eef2f7;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: var(--font-body);
        }

        th {
            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            padding: 16px 12px;
            position: relative;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #eef2f7;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #fafbfe;
        }

        tr:hover {
            background-color: #f0f4ff;
        }

        /* Badge */
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin: 2px 4px 2px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .badge.customer {
            background: var(--color-brand-soft);
            color: var(--color-brand-medium);
            border: 1px solid var(--color-brand-light);
        }

        .badge.staff {
            background: #ede9fe;
            color: #5b21b6;
            border: 1px solid #a78bfa;
        }

        .badge.rider {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #34d399;
        }

        .badge.normal {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        /* Details section */
        td>div {
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 3px solid var(--color-brand-primary-gradient-start);
            margin: 4px 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        td>div strong {
            color: var(--color-brand-dark);
            min-width: 90px;
            display: inline-block;
        }

        /* .btn-add {
                            padding: 10px 20px;
                            border-radius: 8px;
                            cursor: pointer;
                            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
                            color: white;
                            font-weight: 600;
                            transition: 0.3s;
                        }

                        .btn-add:hover {
                            opacity: 0.85;
                        }
                     */
        button.delete-btn {
            background-color: #ef4444;
            color: white;
            border-radius: 8px;
            padding: 6px 12px;
            cursor: pointer;
            font-weight: 600;
        }

        button.delete-btn:hover {
            opacity: 0.85;
        }

        /* Base dropdown */
        select[name="status"] {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
            outline: none;
            position: relative;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg width='10' height='6' viewBox='0 0 10 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill='%23374151' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 10px 6px;
        }

        /* Hover and focus */
        select[name="status"]:hover {
            border-color: #a95c68;
        }

        select[name="status"]:focus {
            border-color: #5a2c2c;
            box-shadow: 0 0 0 2px rgba(233, 202, 199, 0.5);
        }

        /* Options styling - colors */
        select[name="status"] option[value="active"] {
            color: green;
        }

        select[name="status"] option[value="ban"] {
            color: red;
        }

        /* Adding a colored dot before the text using Unicode circle */
        select[name="status"] option[value="active"]::before {
            content: "● ";
            color: green;
        }

        select[name="status"] option[value="ban"]::before {
            content: "● ";
            color: red;
        }

        select[name="status"] option[value="active"] {
            color: green;
        }

        select[name="status"] option[value="ban"] {
            color: red;
        }

        :root {
            --color-brand-dark: #5a2c2c;
            --color-brand-medium: #8F5D54;
            --color-brand-light: #D9CAC7;
            --color-brand-soft: #fff2f5;
            --color-brand-accent: #ffccd9;
            --color-brand-primary-gradient-start: #a95c68;
            --color-brand-primary-gradient-end: #d9999b;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s ease;
        }

        .table-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 32px !important;
            border: 1px solid rgba(169, 92, 104, 0.1);
            position: relative;
            overflow: hidden;
        }

        .table-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg,
                    var(--color-brand-primary-gradient-start),
                    var(--color-brand-primary-gradient-end));
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        #formTitle {
            font-family: inherit;
            font-size: 24px;
            font-weight: 700;
            color: var(--color-brand-dark);
            margin-bottom: 32px !important;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--color-brand-light);
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        #formTitle::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 24px;
            background: linear-gradient(to bottom,
                    var(--color-brand-primary-gradient-start),
                    var(--color-brand-primary-gradient-end));
            border-radius: 3px;
        }

        /* Form Layout */
        form {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            align-items: start;
        }

        /* Responsive grid */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* Form Groups */
        .form-row>div {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* Labels */
        label {
            font-size: 14px;
            font-weight: 600;
            color: var(--color-brand-dark);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        label::after {
            content: '*';
            color: #ef4444;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        label:has(+ input:required)::after,
        label:has(+ select:required)::after {
            opacity: 1;
        }

        /* Input Fields */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(169, 92, 104, 0.2);
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-family: inherit;
            color: var(--color-brand-dark);
            background: white;
            transition: var(--transition);
            outline: none;
            box-shadow: var(--shadow-sm);
        }

        input:focus,
        select:focus {
            border-color: var(--color-brand-primary-gradient-start);
            box-shadow: 0 0 0 3px rgba(169, 92, 104, 0.1);
            transform: translateY(-1px);
        }

        input:hover,
        select:hover {
            border-color: rgba(169, 92, 104, 0.4);
        }

        input::placeholder {
            color: rgba(90, 44, 44, 0.5);
        }

        /* Radio Button Group */
        .radio-group {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            padding: 12px 0;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            cursor: pointer;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(169, 92, 104, 0.1);
            background: var(--color-brand-soft);
            transition: var(--transition);
            margin: 0;
        }

        .radio-group label:hover {
            background: rgba(169, 92, 104, 0.05);
            border-color: rgba(169, 92, 104, 0.3);
        }

        .radio-group input[type="radio"] {
            margin: 0;
            width: 18px;
            height: 18px;
            accent-color: var(--color-brand-primary-gradient-start);
        }

        .radio-group label:has(input:checked) {
            background: linear-gradient(135deg,
                    rgba(169, 92, 104, 0.1),
                    rgba(217, 153, 155, 0.1));
            border-color: var(--color-brand-primary-gradient-start);
            color: var(--color-brand-medium);
            font-weight: 600;
        }

        /* Section Headers for Special Fields */
        #staffFields,
        #riderFields {
            margin-top: 8px;
            padding: 24px;
            background: var(--color-brand-soft);
            border-radius: var(--radius-md);
            border: 1px dashed var(--color-brand-light);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Button Group */
        .button-group {
            display: flex;
            gap: 16px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(169, 92, 104, 0.1);
        }

        .btn-add {
            padding: 14px 32px;
            border-radius: var(--radius-sm);
            border: none;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 120px;
            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
            color: white;
            font-weight: bolder;
        }

        .btn-add[type="submit"] {
            background: linear-gradient(135deg,
                    var(--color-brand-primary-gradient-start),
                    var(--color-brand-primary-gradient-end));
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-add[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-add[type="submit"]:active {
            transform: translateY(0);
        }

        .btn-add[type="button"] {
            background: white;
            color: var(--color-brand-medium);
            border: 1px solid rgba(169, 92, 104, 0.3);
        }

        .btn-add[type="button"]:hover {
            background: var(--color-brand-soft);
            border-color: var(--color-brand-primary-gradient-start);
            transform: translateY(-2px);
        }

        /* Select Styling */
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%235a2c2c' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            padding-right: 48px;
            cursor: pointer;
        }

        /* Validation Error Styling */
        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        input:invalid,
        select:invalid {
            border-color: #fca5a5;
            background: rgba(252, 165, 165, 0.05);
        }

        /* Success/Error States */
        .form-success {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }

        .form-error {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.05);
        }

        /* Loading State */
        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.4),
                    transparent);
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        /* Field Icons (Optional) */
        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-brand-medium);
            pointer-events: none;
        }

        .input-with-icon input {
            padding-left: 48px;
        }

        /* Password Toggle (Optional Enhancement) */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--color-brand-medium);
            cursor: pointer;
            padding: 4px;
            font-size: 12px;
        }

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

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid #10b981;
            color: #065f46;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            color: #7f1d1d;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease;
        }

        .alert-error ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .alert-error li {
            margin-bottom: 4px;
        }

        /* Filters area */
        .filters-container {
            display: flex;
            gap: 12px;
            align-items: center;
            margin: 16px 0 24px;
            padding: 12px 16px;
            border: 1px solid rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .filters-container form {
            flex-direction: row;
        }

        
        .filters-container input[type="text"],
        .filters-container select {
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
@endpush

@section('content')
    <main class="admin-content">
        <div class="container">
            <div class="admin-header">
                <h1><i class="fas fa-user"></i> All Users Management</h1>

                <div style="display:flex; gap:12px; ">
                    <button class="btn-add" onclick="showForm('staff')">
                        <i class="fas fa-user-tie"></i> Add Staff
                    </button>
                    <button class="btn-add" onclick="showForm('rider')">
                        <i class="fas fa-motorcycle"></i> Add Rider
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <div id="addUserForm" style="display:none; margin-bottom:30px;">

                <div class="table-container" style="padding:24px;">
                    <h3 id="formTitle" style="margin-bottom:16px;">Add User</h3>

                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <input type="hidden" name="type" id="userType" value="">

                        <div class="form-row">
                            <div>
                                <label>Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div>
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div>
                                <label>Phone No</label>
                                <input type="text" name="phoneNo" required>
                            </div>
                            <div>
                                <label>Password</label>
                                <input type="password" name="password" required>
                            </div>
                        </div>

                        <!-- Staff only -->
                        <div id="staffFields" style="display:none;">
                            <div class="form-row">
                                <div>
                                    <label>StaffID</label>
                                    <input type="text" name="StaffID">
                                </div>
                                <div>
                                    <label>Outlet</label>
                                    <select name="OutletID">
                                        <option value="">Select Outlet</option>
                                        @foreach ($outlets as $outlet)
                                            <option value="{{ $outlet->OutletID }}">
                                                {{ $outlet->OutletID }} - {{ $outlet->State }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label>Role</label><br>

                                    @php
                                        $roles = ['outlet staff', 'manager']; // or pass from controller
                                    @endphp

                                    @foreach ($roles as $role)
                                        <label style="margin-right:10px;">
                                            <input type="radio" name="Role" value="{{ $role }}">
                                            {{ $role }}
                                        </label>
                                    @endforeach

                                    @error('Role')
                                        <div style="color:red;">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <!-- Rider only -->
                        <div id="riderFields" style="display:none;">
                            <div class="form-row">
                                <div>
                                    <label>RiderID</label>
                                    <input type="text" name="riderID">
                                </div>
                                <div>
                                    <label>Postcode</label>
                                    <input type="text" name="postCode">
                                </div>
                            </div>
                        </div>

                        <div style="margin-top:20px;display:flex; gap:12px;">
                            <button type="submit" class="btn-add">Save</button>
                            <button type="button" class="btn-add"
                                onclick="showForm(document.getElementById('userType').value)">Cancel</button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="tabs">
                <a href="{{ route('admin.users', ['type' => 'all']) }}" class="tab {{ $type == 'all' ? 'active' : '' }}">All</a>
                <a href="{{ route('admin.users', ['type' => 'customer']) }}" class="tab {{ $type == 'customer' ? 'active' : '' }}">Customer</a>
                <a href="{{ route('admin.users', ['type' => 'staff']) }}" class="tab {{ $type == 'staff' ? 'active' : '' }}">Staff</a>
                <a href="{{ route('admin.users', ['type' => 'rider']) }}" class="tab {{ $type == 'rider' ? 'active' : '' }}">Rider</a>
            </div>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('admin.users') }}" class="filters-container" style="flex-direction: row">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by User ID, Name or Email...">
                <button type="submit">Search</button>
                <a href="{{ route('admin.users') }}">Clear</a>
            </form>

            <!-- Table -->
            <div class="table-container" style="overflow:scroll;">
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Details</th>
                            <th colspan="2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr data-type="{{ $user->customer ? 'customer' : ($user->staff ? 'staff' : ($user->rider ? 'rider' : 'normal')) }}"
                                data-userid="{{ strtolower($user->userID) }}" data-name="{{ strtolower($user->name) }}"
                                data-email="{{ strtolower($user->email) }}">
                                <td style="text-align: center">
                                    {{ $user->staff->StaffID ?? ($user->rider->riderID ?? ($user->customer->customerID ?? $user->userID)) }}
                                    <br />
                                    @if ($user->customer)
                                        <span class="badge customer">Customer</span>
                                    @endif
                                    @if ($user->staff)
                                        <span class="badge staff">Staff</span>
                                    @endif
                                    @if ($user->rider)
                                        <span class="badge rider">Rider</span>
                                    @endif
                                    @if (!$user->customer && !$user->staff && !$user->rider)
                                        <span class="badge normal">User</span>
                                    @endif
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    <i class="fas fa-envelope"></i> &nbsp;{{ $user->email }} <br>
                                    <i class="fas fa-phone"></i> &nbsp;{{ $user->phoneNo }}
                                </td>
                                <td>
                                    @if ($user->customer)
                                        <div>
                                            <strong>Customer ID:</strong> {{ $user->customer->customerID }}<br>
                                            <strong>Address:</strong> {{ $user->customer->address ?? '—' }}<br>
                                        </div>
                                    @endif
                                    @if ($user->staff)
                                        <div>
                                            <strong>Staff ID:</strong> {{ $user->staff->StaffID }}<br>
                                            <strong>Role:</strong> {{ $user->staff->Role }}<br>
                                            <strong>Outlet:</strong>
                                            @if ($user->staff->outlet)
                                                {{ $user->staff->OutletID }} - {{ $user->staff->outlet->State }}
                                            @else
                                                {{ $user->staff->OutletID ?? '—' }}
                                            @endif
                                        </div>
                                    @endif
                                    @if ($user->rider)
                                        <div>
                                            <strong>Rider ID:</strong> {{ $user->rider->riderID }}<br>
                                            <strong>Postcode:</strong> {{ $user->rider->postCode }}
                                        </div>
                                    @endif
                                </td>


                                <td>
                                    <form action="{{ route('admin.users.updateStatus', $user->userID) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>
                                                Active ●</option>
                                            <option value="ban" {{ $user->status === 'ban' ? 'selected' : '' }}>Ban ●
                                            </option>
                                        </select>
                                    </form>
                                    <strong>Created At
                                        :</strong>{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    <form action="{{ route('admin.users.destroy', $user->userID) }}" method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;">No users found.</td>
                            </tr>
                        @endempty

                        @if (!$isManager && ($type === 'staff' || $type === 'rider'))
                            <tr>
                                <td colspan="8" style="text-align:center; padding:20px;">
                                    You do not have permission to view this section.
                                </td>
                            </tr>
                        @endif
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</main>
@push('scripts')
    <script>
        function showForm(type) {
            const form = document.getElementById('addUserForm');

            // Toggle: hide if same type clicked
            if (form.style.display === 'block' && document.getElementById('userType').value === type) {
                form.style.display = 'none';
                return;
            }

            form.style.display = 'block';
            document.getElementById('userType').value = type;

            document.getElementById('staffFields').style.display = type === 'staff' ? 'block' : 'none';
            document.getElementById('riderFields').style.display = type === 'rider' ? 'block' : 'none';

            document.getElementById('formTitle').innerText =
                type === 'staff' ? 'Add Staff' : 'Add Rider';
        }

        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('userType').value =
                document.getElementById('staffFields').style.display === 'block' ? 'staff' : 'rider';
        });

        document.querySelectorAll('select[name="status"]').forEach(select => {
            function updateColor() {
                select.style.color = select.value === 'active' ? 'green' : 'red';
            }
            select.addEventListener('change', updateColor);
            updateColor(); // initialize color
        });

        setTimeout(() => {
            document.querySelectorAll('.alert-success, .alert-error')
                .forEach(el => el.style.display = 'none');
        }, 4000);
    </script>
@endpush

@endsection
