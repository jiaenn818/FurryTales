@extends('layouts.admin')

@section('title', 'All Pets Management')

@push('styles')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<main class="admin-content">

    {{-- HEADER --}}
    <div class="admin-header">
        <h1><i class="fas fa-dog"></i> All Pets Management</h1>
        <div class="header-actions">
            <button class="btn btn-primary" id="addCategoryBtn">
                <i class="fas fa-plus-circle"></i> Add Category
            </button>
            <a href="{{ route('admin.pets.add') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Pet
            </a>
            <a href="{{ route('admin.pets.index') }}" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- Messages --}}
    @if(session('category_message'))
    @php $msg = session('category_message') @endphp
    <div class="alert {{ $msg['type']==='success'?'alert-success':'alert-error' }}">
        {{ $msg['text'] }}
    </div>
    @endif

    @if(session('message'))
    <div class="alert {{ session('message_type')==='success'?'alert-success':'alert-error' }}">
        {{ session('message') }}
    </div>
    @endif

    {{-- Category Stats --}}
    @php
    $categoryCounts = [];
    foreach($pets as $pet){
    $type = strtolower(trim($pet->Type));
    if($type) $categoryCounts[$type] = ($categoryCounts[$type] ?? 0) + 1;
    }
    @endphp

    <div class="stats-container">
        <div class="stats-header">
            <h3><i class="fas fa-chart-bar"></i> Pet Statistics</h3>
            <button class="btn btn-sm btn-secondary" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-paw"></i></div>
                <h3>Total Pets</h3>
                <div class="number">{{ $pets->count() }}</div>
                <div class="stat-trend">All categories</div>
            </div>
            @foreach($categories as $cat)
            @php
            $type = strtolower($cat->category_name);
            $count = $categoryCounts[$type] ?? 0;
            $percent = $pets->count() ? round(($count/$pets->count())*100,1) : 0;
            @endphp
            <div class="stat-card" data-type="{{ $type }}">
                <div class="stat-icon">
                    <i class="fas fa-paw"></i>
                </div>
                <h3>{{ $cat->category_name }}</h3>
                <div class="number">{{ $count }}</div>
                <div class="stat-trend">{{ $percent }}% of total</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- PET LIST --}}
    <div class="pets-count">
        <div class="pets-count-header">
            <h2><i class="fas fa-list"></i> All Pets List</h2>
            <div class="category-filter">
                <select id="categoryFilter" class="filter-select">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ strtolower($cat->category_name) }}">{{ $cat->category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <p>Showing {{ $pets->count() }} pets in the system</p>
    </div>

    <div class="pets-search">
        <input type="text" id="petSearch" placeholder="Search pets by name, breed, ID..." autocomplete="off">
        <button class="clear-btn" id="clearSearch"><i class="fas fa-times"></i></button>
    </div>

    @if($pets->isEmpty())
    <div class="no-pets">
        <i class="fas fa-box-open"></i>
        <h3>No pets found</h3>
        <a href="{{ route('admin.pets.add') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add First Pet
        </a>
    </div>
    @else
    <div class="pets-table-container">
        <table class="pets-table" id="petsTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Pet</th>
                    <th>Type / Breed</th>
                    <th>Age / Gender</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pets as $pet)
                @php
                $outlet = $pet->outlet; 
                $supplier = $pet->supplier;
                $images = array_filter([
                $pet->ImageURL1,
                $pet->ImageURL2,
                $pet->ImageURL3,
                $pet->ImageURL4,
                $pet->ImageURL5
                ]);
                @endphp

                <tr data-type="{{ strtolower($pet->Type) }}">
                    {{-- Main Image --}}
                    <td>
                        <img src="{{ $pet->ImageURL1 ? asset($pet->ImageURL1) : asset('image/default-pet.png') }}"
                            class="pet-thumbnail">
                    </td>

                    {{-- Pet Info --}}
                    <td>
                        <strong>{{ $pet->PetName }}</strong>
                        <div>ID: {{ $pet->PetID }}</div>
                    </td>
                    <td>{{ $pet->Type }}<br><small>{{ $pet->Breed }}</small></td>
                    <td>{{ $pet->Age }} months<br><small>{{ $pet->Gender }}</small></td>
                    <td>{{ $pet->HealthStatus }}<br>{{ $pet->VaccinationStatus }}</td>
                    <td>RM {{ number_format($pet->Price,2) }}</td>

                    {{-- Actions --}}
                    <td>
                        <button class="btn btn-success btn-sm"
                            onclick="document.getElementById('petPopup{{ $pet->PetID }}').style.display='flex'">
                            View
                        </button>

                        <a href="{{ route('admin.pets.edit', $pet->PetID) }}"
                            class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <form action="{{ route('admin.pets.destroy', $pet->PetID) }}"
                            method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this pet?')">
                                Delete
                            </button>
                        </form>
                    </td>
                    {{-- Pet Popup --}}
                    <div class="popup-overlay" id="petPopup{{ $pet->PetID }}">
                        <div class="popup-box">
                            <span class="popup-close" onclick="this.closest('.popup-overlay').style.display='none'">&times;</span>

                            {{-- Main Popup Image --}}
                            <img class="popup-image" src="{{ $pet->ImageURL1 ? asset($pet->ImageURL1) : asset('image/default-pet.png') }}">

                            {{-- Thumbnails --}}
                            <div class="popup-thumbs">
                                @foreach($images as $img)
                                <img src="{{ asset($img) }}"
                                    data-src="{{ asset($img) }}"
                                    onclick="this.closest('.popup-box').querySelector('.popup-image').src=this.dataset.src">
                                @endforeach
                            </div>

                            {{-- Pet Details --}}
                            <h3>{{ $pet->PetName }}</h3>
                            <p><strong>ID:</strong> {{ $pet->PetID }}</p>
                            <p><strong>Type:</strong> {{ $pet->Type }}</p>
                            <p><strong>Breed:</strong> {{ $pet->Breed }}</p>
                            <p><strong>Age:</strong> {{ $pet->Age }} months</p>
                            <p><strong>Gender:</strong> {{ $pet->Gender }}</p>
                            <p><strong>Price:</strong> RM {{ number_format($pet->Price,2) }}</p>
                            <p><strong>Health Status:</strong> {{ $pet->HealthStatus }}</p>
                            <p><strong>Vaccination Status:</strong> {{ $pet->VaccinationStatus }}</p>
                            <p><strong>Outlet:</strong> {{ $outlet ? $outlet->OutletID . ' - ' . $outlet->City . '  (' . $outlet->State . ')' : 'N/A' }}</p>
                            <p><strong>Supplier:</strong> {{ $supplier ? $supplier->SupplierID . ' - ' . $supplier->SupplierName : 'N/A' }}</p>
                            <p><strong>Description:</strong> {!! nl2br(e($pet->Description)) !!}</p>
                        </div>
                    </div>
                    @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Add Category Modal --}}
    <div id="addCategoryModal" class="modal-overlay">
        <div class="modal-content">
            <h2><i class="fas fa-folder-plus"></i> Add Category</h2>
            <form action="{{ route('admin.categories.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="category_name" required>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" id="closeModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Category modal
    const addBtn = document.getElementById('addCategoryBtn');
    const modal = document.getElementById('addCategoryModal');
    const closeBtn = document.getElementById('closeModal');
    addBtn?.addEventListener('click', () => modal.style.display = 'flex');
    closeBtn?.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', e => {
        if (e.target === modal) modal.style.display = 'none';
    });

    // Category filter
    document.getElementById('categoryFilter')?.addEventListener('change', function() {
        const value = this.value;
        const rows = document.querySelectorAll('#petsTable tbody tr');
        let count = 0;
        rows.forEach(r => {
            if (value === 'all' || r.dataset.type === value) {
                r.style.display = '';
                count++;
            } else {
                r.style.display = 'none';
            }
        });
        document.querySelector('.pets-count p').textContent = `Showing ${count} pets in the system`;
    });

    // Search bar
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('petSearch');
        const clearBtn = document.getElementById('clearSearch');
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.focus();
            document.querySelectorAll('#petsTable tbody tr').forEach(r => r.style.display = '');
        });
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.toLowerCase();
            document.querySelectorAll('#petsTable tbody tr').forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(term) ? '' : 'none';
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        // AI Detection Elements
        const imageInput = document.getElementById("imageInput");
        const preview = document.getElementById("preview");
        const breedInput = document.getElementById("breed");
        const typeInput = document.getElementById("type");
        const overlay = document.getElementById("loadingOverlay");

        // Multiple Images Elements
        const addImageBtn = document.getElementById("addImageBtn");
        const extraImagesContainer = document.getElementById("extraImagesContainer");
        const maxImages = 5;
        let imageCount = 1; // Start with 1 (main image)

        // ========== AI BREED DETECTION ==========
        if (imageInput) {
            imageInput.addEventListener("change", async function() {
                if (!this.files[0]) return;

                const file = this.files[0];

                // Show preview immediately
                preview.src = URL.createObjectURL(file);

                // Show loading overlay
                if (overlay && window.showLoadingOverlay) {
                    window.showLoadingOverlay();
                } else {
                    overlay.style.display = "flex";
                }

                const formData = new FormData();
                formData.append("image", file);

                try {
                    const res = await fetch("index.php?action=detectBreed", {
                        method: "POST",
                        body: formData
                    });
                    const data = await res.json();

                    console.log("AI Response:", data);

                    if (data.breed) breedInput.value = data.breed;
                    if (data.type) typeInput.value = data.type;

                    if (data.debug) console.log("Debug:", data.debug);

                } catch (err) {
                    console.error("AI detection failed:", err);
                    alert("AI detection failed! Please try again.");
                } finally {
                    // Hide loading overlay
                    if (overlay && window.hideLoadingOverlay) {
                        window.hideLoadingOverlay();
                    } else {
                        setTimeout(() => {
                            overlay.style.display = "none";
                        }, 300);
                    }
                }
            });
        }

        // ========== MULTIPLE IMAGES FUNCTIONALITY ==========

        // Function to create a new image input field
        function createImageInput(index) {
            const imageItem = document.createElement('div');
            imageItem.className = 'image-item';
            imageItem.dataset.index = index;

            const label = document.createElement('label');
            label.htmlFor = `extraImage${index}`;

            const img = document.createElement('img');
            img.src = 'public/uploads/placeholder.png';
            img.className = 'extra-preview';
            img.id = `extraPreview${index}`;
            img.alt = `Extra image ${index}`;

            const p = document.createElement('p');
            p.textContent = `Image ${index + 1}`;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-image-btn';
            removeBtn.innerHTML = 'x';
            removeBtn.title = 'Remove this image';

            const input = document.createElement('input');
            input.type = 'file';
            input.id = `extraImage${index}`;
            input.name = `extraImage${index + 1}`;
            input.accept = 'image/*';
            input.hidden = true;

            // Preview for extra image
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Remove button functionality
            removeBtn.addEventListener('click', function() {
                imageItem.remove();
                imageCount--;
                updateAddButton();
                updateImageNumbers();
            });

            label.appendChild(img);
            label.appendChild(p);
            imageItem.appendChild(label);
            imageItem.appendChild(removeBtn);
            imageItem.appendChild(input);

            return imageItem;
        }

        // Update add button state and counter
        function updateAddButton() {
            if (imageCount >= maxImages) {
                addImageBtn.disabled = true;
                addImageBtn.textContent = 'Maximum 5 images reached';
            } else {
                addImageBtn.disabled = false;
                addImageBtn.innerHTML = `+ Add Another Image <span class="image-counter">(${imageCount}/${maxImages})</span>`;
            }
        }

        // Update image numbers
        function updateImageNumbers() {
            const imageItems = document.querySelectorAll('.image-item');
            imageItems.forEach((item, index) => {
                const p = item.querySelector('p');
                if (p) {
                    p.textContent = `Image ${index + 2}`; // +2 because main is image 1
                }
                item.dataset.index = index;
            });
        }

        // Add new image button click
        if (addImageBtn) {
            // Initial button text with counter
            addImageBtn.innerHTML = `+ Add Another Image <span class="image-counter">(${imageCount}/${maxImages})</span>`;

            addImageBtn.addEventListener('click', function() {
                if (imageCount < maxImages) {
                    const newIndex = imageCount - 1;
                    const newImageInput = createImageInput(newIndex);
                    extraImagesContainer.appendChild(newImageInput);
                    imageCount++;
                    updateAddButton();

                    // Auto-click the new file input
                    setTimeout(() => {
                        document.getElementById(`extraImage${newIndex}`).click(); // Use backticks here too
                    }, 100);
                }
            });
        }

        // Initialize the button state
        updateAddButton();
    });
</script>
@endpush