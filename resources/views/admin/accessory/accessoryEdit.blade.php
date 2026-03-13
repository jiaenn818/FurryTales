@extends('layouts.admin')

@section('title', 'Edit Accessory')

@push('styles')

@endpush


@section('content')
<form action="{{ route('admin.accessories.update', $accessory->AccessoryID) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Tabs --}}
    <div class="form-tabs">
        <button type="button" class="tab-btn active" data-tab="basic">Basic Info</button>
        <button type="button" class="tab-btn" data-tab="images">Images</button>
        <button type="button" class="tab-btn" data-tab="variants">Variants</button>
    </div>

    {{-- Basic Info --}}
    <div id="basic" class="tab-content active">
        <div class="form-row">
            <div class="form-group">
                <label>Accessory ID</label>
                <input type="text" name="AccessoryID" value="{{ old('AccessoryID', $accessory->AccessoryID) }}" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label>Accessory Name*</label>
                <input type="text" name="AccessoryName" value="{{ old('AccessoryName', $accessory->AccessoryName) }}" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Category*</label>
                <select name="Category" class="form-control" required>
                    <option value="">Select Category</option>
                    @foreach($categoryOptions as $cat)
                    <option value="{{ $cat }}"
                        {{ old('Category', $accessory->Category) == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Supplier*</label>
                <select name="SupplierID" class="form-control @error('SupplierID') is-invalid @enderror" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->SupplierID }}"
                        {{ old('SupplierID', $accessory->SupplierID) == $supplier->SupplierID ? 'selected' : '' }}>
                        {{ $supplier->SupplierName }}
                    </option>
                    @endforeach
                </select>
                @error('SupplierID')
                <div class="form-text text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Brand*</label>
                <input type="text" name="Brand" value="{{ old('Brand', $accessory->Brand) }}" class="form-control">
            </div>
            <div class="form-group">
                <label>Description (optional)</label>
                <textarea name="Description" class="form-control" rows="4">{{ old('Description', $accessory->Description) }}</textarea>
            </div>
        </div>
    </div>
    {{-- Images --}}
    <div id="images" class="tab-content">
        <div class="form-row">
            @for($i=1; $i<=5; $i++)
                <div class="upload-box" id="uploadBox-{{ $i }}">

                {{-- Placeholder / click to upload --}}
                <div class="upload-placeholder" onclick="document.getElementById('image-{{ $i }}').click()">
                    <i class="fas fa-cloud-upload-alt"></i> Image {{ $i }}
                </div>

                {{-- Preview --}}
                <img id="preview-{{ $i }}"
                    src="{{ $accessory->{'ImageURL'.$i} ? asset($accessory->{'ImageURL'.$i}) : '' }}"
                    class="{{ $accessory->{'ImageURL'.$i} ? 'd-block' : 'd-none' }}">

                {{-- File input --}}
                <input type="file" name="ImageURL{{ $i }}" id="image-{{ $i }}"
                    accept="image/*" onchange="previewImage(this,'{{ $i }}')" style="display: none;">

                {{-- Hidden input to mark deletion --}}
                <input type="hidden" name="removeImage{{ $i }}" id="removeImage{{ $i }}" value="0">

                {{-- Remove button --}}
                <button type="button" class="remove-image {{ $accessory->{'ImageURL'.$i} ? 'd-flex' : 'd-none' }}"
                    onclick="removeImage('{{ $i }}')">
                    <i class="fas fa-times"></i>
                </button>

        </div>
        @endfor
    </div>
    </div>

    {{-- Variants --}}
    <div id="variants" class="tab-content variants-section">
        <div id="variant-list">
            @foreach($accessory->variants as $vIndex => $variant)
            <div class="variant-item" data-index="{{ $vIndex }}">
                <div class="variant-header">
                    <h4>Variant #{{ $vIndex + 1 }}</h4>
                    <button type="button" class="remove-variant" onclick="removeVariant(this)">Remove</button>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Variant Key</label>
                        <input type="text" name="variants[{{ $vIndex }}][VariantKey]" class="form-control"
                            value="{{ old("variants.$vIndex.VariantKey", $variant->VariantKey) }}">
                    </div>
                    <div class="form-group">
                        <label>Price (RM)</label>
                        <input type="number" name="variants[{{ $vIndex }}][Price]" class="form-control" step="0.01"
                            value="{{ old("variants.$vIndex.Price", $variant->Price) }}">
                    </div>
                </div>

                {{-- Outlet Stock --}}
                <div class="outlet-stock-section">
                    <h5>Outlet Stock</h5>
                    <div class="outlet-stock-list">
                        @php
                        $outletStocks = $accessory->findOutletStockByVariantID($variant->VariantID);
                        @endphp

                        @if($outletStocks && count($outletStocks) > 0)
                        @foreach($outletStocks as $oIndex => $stock)
                        <div class="outlet-stock-item" data-index="{{ $oIndex }}">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Outlet</label>
                                    <select name="variants[{{ $vIndex }}][outlets][{{ $oIndex }}][OutletID]" class="form-control">
                                        <option value="">Select Outlet</option>
                                        @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->OutletID }}"
                                            {{ $stock['OutletID'] == $outlet->OutletID ? 'selected' : '' }}>
                                            {{ $outlet['State'] }} -- {{ $outlet['City'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Stock Quantity</label>
                                    <input type="number" name="variants[{{ $vIndex }}][outlets][{{ $oIndex }}][StockQty]"
                                        class="form-control" min="0" value="{{ $stock['StockQty'] }}">
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger" onclick="removeOutletStock(this)">Remove</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        {{-- Default empty outlet stock row --}}
                        <div class="outlet-stock-item" data-index="0">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Outlet</label>
                                    <select name="variants[{{ $vIndex }}][outlets][0][OutletID]" class="form-control">
                                        <option value="">Select Outlet</option>
                                        @foreach($outlets as $outlet)
                                            <option value="{{ $outlet->OutletID }}">{{ $outlet->State }} -- {{ $outlet->City }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Stock Quantity</label>
                                    <input type="number" name="variants[{{ $vIndex }}][outlets][0][StockQty]" class="form-control" min="0" value="0">
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger" onclick="removeOutletStock(this)">Remove</button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-primary" onclick="addOutletStock(this)">Add Outlet Stock</button>
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-success" onclick="addVariant()">Add Variant</button>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary mt-3">Update Accessory</button>
    </div>
</form>

{{-- Outlet Stock Template for JS --}}
<script type="text/html" id="outletStockTemplate">
    <div class="outlet-stock-item" data-index="__INDEX__">
        <div class="form-row">
            <div class="form-group">
                <label>Outlet</label>
                <select name="variants[__VARIANT__][outlets][__INDEX__][OutletID]" class="form-control">
                    <option value="">Select Outlet</option>
                    @foreach($outlets as $outlet)
                    <option value="{{ $outlet->OutletID }}">{{ $outlet->State }} -- {{ $outlet->City }}</option>
                    @endforeach
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

@endsection

@push('scripts')
<script src="{{ asset('js/accessory.js') }}"></script>

@endpush