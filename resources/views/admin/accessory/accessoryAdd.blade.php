    @extends('layouts.admin')

    @section('title', 'Add New Accessory')


    @push('styles')

    @endpush
    @section('content')
    <main class="admin-main">
        <div class="add-header">
            <h1><i class="fas fa-plus-circle"></i> Add New Accessory</h1>
            <p>Create a new accessory with details, images, variants, and stock information</p>
            <a href="{{ route('admin.accessories.index') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.accessories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

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
                        <input type="text" name="AccessoryID" value="{{ old('AccessoryID', $nextAccessoryID) }}" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Accessory Name*</label>
                        <input type="text" name="AccessoryName" value="{{ old('AccessoryName') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category*</label>
                        <select name="Category" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categoryOptions as $cat)
                            <option value="{{ $cat }}" {{ old('Category')==$cat?'selected':'' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Supplier*</label>
                        <select name="SupplierID" class="form-control @error('SupplierID') is-invalid @enderror" required>
                            <option value="">Select Supplier</option>

                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->SupplierID }}"
                                {{ old('SupplierID') == $supplier->SupplierID ? 'selected' : '' }}>
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
                        <input type="text" name="Brand" value="{{ old('Brand') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Description (optional)</label>
                        <textarea name="Description" class="form-control" rows="4">{{ old('Description') }}</textarea>
                    </div>
                </div>
            </div>


            {{-- Images --}}
            <div id="images" class="tab-content">
                <div class="form-row">
                    @for($i=1;$i<=5;$i++)
                        <div class="upload-box" id="uploadBox-{{ $i }}">
                        <div class="upload-placeholder" onclick="document.getElementById('image-{{ $i }}').click()">
                            <i class="fas fa-cloud-upload-alt"></i> Image {{ $i }}
                        </div>
                        <img id="preview-{{ $i }}" src="{{ asset('image/default-pet.png') }}" style="display:none;">
                        <input type="file" name="ImageURL{{ $i }}" id="image-{{ $i }}"
                            accept="image/*" onchange="previewImage(this,'{{ $i }}')" style="display: none;">
                        <button type="button" class="remove-image" onclick="removeImage('{{ $i }}')" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                </div>
                @endfor
            </div>
            </div>

            {{-- Variants --}}
            <div id="variants" class="tab-content variants-section">
                <div id="variant-list">
                    <div class="variant-item" data-index="0">
                        <div class="variant-header">
                            <h4>Variant #1*</h4><button type="button" class="remove-variant" onclick="removeVariant(this)">Remove</button>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Variant Key*</label>
                                <input type="text" name="variants[0][VariantKey]" class="form-control">
                                <br/>
                                <p style="opacity:50%;">e.g., Color:Red|Size:Small|Material:Cotton</p>
                            </div>
                            <div class="form-group">
                                <label>Price (RM)*</label>
                                <input type="number" name="variants[0][Price]" class="form-control" step="0.01" value="0">
                            </div>
                        </div>

                        {{-- Outlet-stock --}}
                        <div class="outlet-stock-section">
                            <h5>Outlet Stock</h5>
                            <div class="outlet-stock-list">
                                <div class="outlet-stock-item" data-index="0">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Outlet*</label>
                                            <select name="variants[0][outlets][0][OutletID]" class="form-control">
                                                <option value="">Select Outlet</option>
                                                @foreach($outlets as $outlet)
                                                <option value="{{ $outlet->OutletID }}">{{ $outlet->State }} -- {{ $outlet->City }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Stock Quantity*</label>
                                            <input type="number" name="variants[0][outlets][0][StockQty]" class="form-control" min="0" value="0">
                                        </div>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger" onclick="removeOutletStock(this)">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="addOutletStock(this)">Add Outlet Stock</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success" onclick="addVariant()">Add Variant</button>
            </div>


            {{-- Submit --}}
            <div class="form-group">
                <button type="submit" class="btn btn-success">Save Accessory</button>
            </div>
        </form>

        {{-- Hidden Template --}}
        <div id="outletStockTemplate" style="display:none;">
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
        </div>
    </main>
    @endsection

    @push('scripts')
    <script src="{{ asset('js/accessory.js') }}">
    </script>

    <script>
        // Your validation / form JS here
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');

            form.addEventListener('submit', function(e) {
                let basicValid = true;
                let variantValid = false;

                // Check required basic info fields
                const requiredFields = ['AccessoryName', 'Category', 'SupplierID', 'Brand'];
                requiredFields.forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (!field || field.value.trim() === '') {
                        basicValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Check if at least one variant exists
                const variants = document.querySelectorAll('#variant-list .variant-item');
                variants.forEach(variant => {
                    const outlets = variant.querySelectorAll('.outlet-stock-item');
                    if (outlets.length > 0) variantValid = true;
                });

                if (!basicValid) {
                    alert('Please fill all required basic info fields.');
                    e.preventDefault();
                    return false;
                }

                if (!variantValid) {
                    alert('Please add at least one variant with outlet stock.');
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>


    @endpush