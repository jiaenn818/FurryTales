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

    ul{
      list-style: none;  
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
    @if(session('message'))
    <div class="alert {{ session('message_type')==='success'?'alert-success':'alert-error' }}">
        {{ session('message') }}
    </div>
    @endif

    {{-- ACCESSORY LIST --}}
    <div class="pets-search">
        <input type="text" id="accessorySearch" placeholder="Search accessories by name, ID..." autocomplete="off">
        <button class="clear-btn" id="clearSearch"><i class="fas fa-times"></i></button>
    </div>

    @if($accessories->isEmpty())
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
                @foreach($accessories as $acc)
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
                            <button type="submit"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this accessory?')">
                                Delete
                            </button>
                        </form>
                    </td>

                    {{-- Popup --}}
                    <div class="popup-overlay" id="accessoryPopup{{ $acc->AccessoryID }}">
                        <div class="popup-box">
                            <span class="popup-close" onclick="this.closest('.popup-overlay').style.display='none'">&times;</span>

                            {{-- Main Image --}}
                            <img class="popup-image" src="{{ asset($acc->ImageURL1 ?: 'image/default-pet.png') }}">

                            {{-- Extra Images --}}
                            <div class="popup-thumbs">
                                @foreach(array_filter([$acc->ImageURL2, $acc->ImageURL3, $acc->ImageURL4, $acc->ImageURL5]) as $img)
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
                            <p style="color:--color-brand-accent;">------------------------------------------------------------------------------------------------------------------</p>
                            {{-- Variants --}}
                            <h3>Variants</h3>
                            @if($acc->variants->isEmpty())
                            <p>No variants</p>
                            @else
                            <ul>
                                @foreach($acc->variants as $var)
                                <li>{{ $var->VariantKey }} @ RM {{ number_format($var->Price,2) }}</li>
                                @endforeach
                            </ul>
                            @endif
                            
                            <p style="color:--color-brand-accent;">------------------------------------------------------------------------------------------------------------------</p>
                            {{-- Outlet Stock --}}
                            <h3>Outlet Stock</h3>

                            @if($acc->outletAccessories->isEmpty())
                                <p>No outlet stock</p>
                            @else
                                <ul class="no-bullet">
                                    @foreach(    $acc->outletAccessories->sortBy(fn($oa) => $oa->outlet->State ?? '') as $oa)
                                        <li>
                                            <strong><i>{{ $oa->outlet->State ?? $oa->OutletID }}</i></strong> -- {{ $oa->outlet->City }} &nbsp;
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
    </div>
    @endif
</main>
@endsection

@push('scripts')
<script>
    // Search
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('accessorySearch');
        const clearBtn = document.getElementById('clearSearch');
        const rows = document.querySelectorAll('#accessoriesTable tbody tr');

        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            rows.forEach(r => r.style.display = '');
        });

        searchInput.addEventListener('input', () => {
            const term = searchInput.value.toLowerCase();
            rows.forEach(r => {
                r.style.display = r.dataset.name.includes(term) ? '' : 'none';
            });
        });
    });
</script>
@endpush