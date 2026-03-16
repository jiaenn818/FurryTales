@extends('layouts.admin')

@section('title', 'All Accessories Management')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .accessory-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .popup-thumbs img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            margin: 5px;
            cursor: pointer;
            border-radius: 5px;
        }

        .popup-image {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        ul {
            list-style: none;
        }

        /* Filters area */
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
@endpush

@section('content')
    <main class="admin-content">
        <div class="admin-header">
            <h1><i class="fa-sharp fa-solid fa-bone"></i> All Accessories Management</h1>
            <div class="header-actions">
                <a href="{{ route('admin.accessories.add') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Accessory
                </a>
            </div>
        </div>

        {{-- Messages --}}
        @if (session('message'))
            <div class="alert {{ session('message_type') === 'success' ? 'alert-success' : 'alert-error' }}">
                {{ session('message') }}
            </div>
        @endif

        {{-- ACCESSORY LIST --}}
        <form method="GET" action="{{ route('admin.accessories.index') }}" class="filters-container">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Name, ID, Category">

            <select name="category">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->Category }}" {{ request('category') == $cat->Category ? 'selected' : '' }}>
                        {{ $cat->Category }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
            <a href="{{ route('admin.accessories.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i>
                Clear</a>
        </form>

        @if ($accessories->isEmpty())
            <div class="no-pets">
                <i class="fas fa-box-open"></i>
                <h3>No accessories found</h3>
                <a href="{{ route('admin.accessories.add') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Accessory
                </a>
            </div>
        @else
            <div class="pets-table-container" style="overflow: scroll">
                <table class="pets-table" id="accessoriesTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Accessory</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accessories as $acc)
                            <tr data-name="{{ strtolower($acc->AccessoryName) }}">
                                <td>
                                    <img src="{{ asset($acc->ImageURL1 ?: 'image/default-pet.png') }}"
                                        class="accessory-thumbnail">
                                </td>
                                <td>
                                    <strong>{{ $acc->AccessoryName }}</strong>
                                    <div>ID: {{ $acc->AccessoryID }}</div>
                                </td>
                                <td>{{ $acc->Category }}</td>
                                <td>{{ $acc->outletAccessories->sum('StockQty') ?? 0 }}</td>
                                <td>
                                    {{-- View Button --}}
                                    <button class="btn btn-success btn-sm"
                                        onclick="document.getElementById('accessoryPopup{{ $acc->AccessoryID }}').style.display='flex'">
                                        View
                                    </button>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.accessories.edit', $acc->AccessoryID) }}"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.accessories.destroy', $acc->AccessoryID) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this accessory?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>

                                {{-- Popup --}}
                                <div class="popup-overlay" id="accessoryPopup{{ $acc->AccessoryID }}">
                                    <div class="popup-box">
                                        <span class="popup-close"
                                            onclick="this.closest('.popup-overlay').style.display='none'">&times;</span>

                                        {{-- Main Image --}}
                                        <img class="popup-image"
                                            src="{{ asset($acc->ImageURL1 ?: 'image/default-pet.png') }}">

                                        {{-- Extra Images --}}
                                        <div class="popup-thumbs">
                                            @foreach (array_filter([$acc->ImageURL2, $acc->ImageURL3, $acc->ImageURL4, $acc->ImageURL5]) as $img)
                                                <img src="{{ asset($img) }}"
                                                    onclick="this.closest('.popup-box').querySelector('.popup-image').src=this.src">
                                            @endforeach
                                        </div>

                                        {{-- Accessory Details --}}
                                        <h2>{{ $acc->AccessoryName }}</h2>
                                        <p><strong>ID:</strong> {{ $acc->AccessoryID }}</p>
                                        <p><strong>Category:</strong> {{ $acc->Category }}</p>
                                        <p><strong>Brand:</strong> {{ $acc->Brand }}</p>
                                        <p><strong>Description:</strong> {!! nl2br(e($acc->Description)) !!}</p>
                                        <p style="color:--color-brand-accent;">
                                            ------------------------------------------------------------------------------------------------------------------
                                        </p>
                                        {{-- Variants --}}
                                        <h3>Variants</h3>
                                        @if ($acc->variants->isEmpty())
                                            <p>No variants</p>
                                        @else
                                            <ul>
                                                @foreach ($acc->variants as $var)
                                                    <li>{{ $var->VariantKey }} @ RM {{ number_format($var->Price, 2) }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        <p style="color:--color-brand-accent;">
                                            ------------------------------------------------------------------------------------------------------------------
                                        </p>
                                        {{-- Outlet Stock --}}
                                        <h3>Outlet Stock</h3>

                                        @if ($acc->outletAccessories->isEmpty())
                                            <p>No outlet stock</p>
                                        @else
                                            <ul class="no-bullet">
                                                @foreach ($acc->outletAccessories->sortBy(fn($oa) => $oa->outlet->State ?? '') as $oa)
                                                    <li>
                                                        <strong><i>{{ $oa->outlet->State ?? $oa->OutletID }}</i></strong>
                                                        -- {{ $oa->outlet->City }} &nbsp;
                                                        | {{ $oa->variant->VariantKey ?? 'No Variant' }}
                                                        — Stock: {{ $oa->StockQty }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                    </div>
                                </div>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $accessories->links() }}
                </div>
            </div>
        @endif
    </main>
@endsection

@push('scripts')
@endpush
