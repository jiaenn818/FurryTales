@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/app.css') }}">

{{-- Image Search Modal - Moved to root level to avoid ALL stacking issues --}}
<div id="imageModal" class="image-modal" style="display:none">
    <div class="image-modal-content">
        <button type="button" class="modal-close-btn" onclick="closeImageModal()">×</button>
        
        <div class="modal-header-container">
            <h3 class="modal-title">Find Similar Friends</h3>
            <p class="modal-subtitle">Upload a photo of a pet to find its closest matches in our store.</p>
        </div>

        <div id="dropZone" class="drop-zone" onclick="document.getElementById('imageInput').click()">
            <div class="drop-zone-icon">📸</div>
            <p class="drop-zone-text-main">Drag & drop image here</p>
            <p class="drop-zone-text-sub">or click to browse files</p>
        </div>
        
        <input type="file" id="imageInput" accept="image/*" style="display: none;" onchange="handleFile(this.files[0])">

        <img id="previewImage" hidden>

        <div id="loading" hidden class="loading-status">
            Analyzing image...
        </div>

        <div id="results" class="results-grid"></div>
    </div>
</div>


<div class="pets-page">
    <div class="container">
        <div class="pets-layout">

        <!-- Sidebar / Filter Panel -->
        <aside class="pets-sidebar">
            <div class="pets-filter-panel">
                <form id="filterForm" action="{{ route('client.pets.index') }}" method="GET">
                    
                    {{-- Hidden fields to preserve sort and categories when filtering --}}
                    <input type="hidden" name="sort_price" id="hiddenSortPrice" value="{{ request('sort_price') }}">
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    <input type="hidden" name="breed" value="{{ request('breed') }}">
                    <input type="hidden" name="recommend_count" id="hiddenRecommendCount" value="{{ $recommendCount ?? 5 }}">


                    <div class="pets-search-wrapper">
                        <input type="text"
                            name="search"
                            placeholder="Search pets..."
                            value="{{ request('search') }}"
                            class="pets-search-input"
                            autocomplete="off">

                        <button type="submit" class="pets-search-button">
                            🔍
                        </button>
                    </div>

                    {{-- Search History Below Search Bar --}}
                    @if(isset($searchHistories) && $searchHistories->count())
                    <div id="petsSearchHistoryWrapper" class="pets-search-history-section">

                        <div class="pets-search-history-header">
                            <span class="pets-search-history-title">
                                Recent Searches
                            </span>

                            <div class="pets-search-history-actions">
                                <button type="button"
                                        onclick="clearAllSearchHistory()"
                                        class="pets-search-history-clear">
                                    Clear All
                                </button>

                                <button type="button"
                                        onclick="toggleSearchHistory()"
                                        class="pets-search-history-toggle"
                                        id="toggleHistoryBtn"
                                        title="Toggle search history">
                                    <span id="toggleIcon">−</span>
                                </button>
                            </div>
                        </div>

                        <div id="petsSearchHistoryBody" class="pets-search-history-body">
                            @foreach($searchHistories as $history)
                                <div class="pets-search-card" id="search-history-{{ $history->SearchHistoryID }}">
                                    <a href="{{ route('client.pets.index', ['search' => $history->keyword]) }}"
                                    class="pets-search-link">
                                        {{ $history->keyword }}
                                    </a>
                                    <button type="button"
                                            onclick="deleteSearchHistory({{ $history->SearchHistoryID }})"
                                            class="pets-search-card-delete">
                                        ×
                                    </button>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    @endif

                    <button type="button" class="pets-image-search-button" onclick="openImageModal()">
                        📸 Search by Photo
                    </button>

                    <!-- Categories -->
                    <div class="pets-category-section">
                        @php
                            $allPetsParams = request()->query();
                            unset($allPetsParams['type'], $allPetsParams['breed']);
                            $isActiveAll = !request('type');
                        @endphp
                        <h4 class="pets-category-header">
                            <a href="{{ route('client.pets.index', $allPetsParams) }}" 
                               class="pets-category-title {{ $isActiveAll ? 'active' : '' }}">
                               All Pets
                            </a>
                        </h4>
                    </div>

                    @foreach($types as $typeObj)
                        @php
                            $type = $typeObj->Type;
                        @endphp
                        <div class="pets-category-section">
                            @php
                                $newParams = request()->query();
                                $newParams['type'] = $type;
                                unset($newParams['breed'], $newParams['search']);
                                $isActiveType = request('type') === $type;
                            @endphp


                            <h4 class="pets-category-header">
                                <a href="{{ route('client.pets.index', $newParams) }}" 
                                   class="pets-category-title {{ $isActiveType ? 'active' : '' }}">
                                   {{ $type }}s
                                </a>
                                <span class="pets-category-badge">{{ isset($breeds[$type]) ? count($breeds[$type]) : 0 }}</span>
                            </h4>

                            @if($isActiveType)
                                <div class="pets-breed-select-wrapper" style="margin-top: 10px;">
                                    <select name="breed" onchange="this.form.submit()" class="pets-select">
                                        <option value="">All {{ $type }} Breeds</option>
                                        @if(isset($breeds[$type]))
                                            @foreach($breeds[$type] as $breed)
                                                <option value="{{ $breed->Breed }}"
                                                    {{ request('breed') === $breed->Breed ? 'selected' : '' }}>
                                                    {{ $breed->Breed }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif
                        </div>
                    @endforeach


                    <!-- Outlets -->
                    <div class="pets-category-section">
                        <h4 class="pets-category-header">
                            <span class="pets-category-title">Outlet</span>
                        </h4>
                        <select name="outlet" onchange="this.form.submit()" class="pets-select">
                            <option value="">All Outlets</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->City }}"
                                    {{ request('outlet') === $outlet->City ? 'selected' : '' }}>
                                    {{ $outlet->City }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Filter -->
                    <div class="pets-price-filter">
                        <div class="pets-price-header">
                            <h4>Price Range</h4>
                            <button type="button" onclick="resetPriceFilter()" class="pets-price-reset">
                                Reset
                            </button>
                        </div>
                        
                        @php
                            $min = request('min_price') ?? ($dbMinPrice ?? 0);
                            $max = request('max_price') ?? ($dbMaxPrice ?? 9999);
                            $dbMin = $dbMinPrice ?? 0;
                            $dbMax = $dbMaxPrice ?? 9999;
                        @endphp

                        <div class="pets-price-display">
                            <span>RM <span id="minPriceVal">{{ $min }}</span></span>
                            <span>RM <span id="maxPriceVal">{{ $max }}</span></span>
                        </div>

                        <div class="pets-price-slider-wrapper">
                            <!-- Track -->
                            <div class="pets-price-track"></div>

                            <!-- Sliders -->
                            <input type="range" id="minPrice" name="min_price" min="{{ $dbMin }}" max="{{ $dbMax }}" value="{{ $min }}" 
                                class="pets-price-slider">
                            <input type="range" id="maxPrice" name="max_price" min="{{ $dbMin }}" max="{{ $dbMax }}" value="{{ $max }}" 
                                class="pets-price-slider">
                        </div>

                        <button type="submit" class="pets-apply-button">
                            Apply Filters
                        </button>
                    </div>

                    <script>
                        const minSlider = document.getElementById('minPrice');
                        const maxSlider = document.getElementById('maxPrice');
                        const minVal = document.getElementById('minPriceVal');
                        const maxVal = document.getElementById('maxPriceVal');
                        const dbMin = {{ $dbMin }};
                        const dbMax = {{ $dbMax }};

                        function syncMin() {
                            if (+minSlider.value > +maxSlider.value) minSlider.value = maxSlider.value;
                            minVal.innerText = minSlider.value;
                            updateSliderZIndex(minSlider);
                        }
                        function syncMax() {
                            if (+maxSlider.value < +minSlider.value) maxSlider.value = minSlider.value;
                            maxVal.innerText = maxSlider.value;
                            updateSliderZIndex(maxSlider);
                        }
                        
                        function updateSliderZIndex(activeSlider) {
                            if (activeSlider.id === 'minPrice') {
                                minSlider.style.zIndex = "20";
                                maxSlider.style.zIndex = "10";
                            } else {
                                minSlider.style.zIndex = "10";
                                maxSlider.style.zIndex = "20";
                            }
                        }

                        function resetPriceFilter() {
                            minSlider.value = dbMin;
                            maxSlider.value = dbMax;
                            syncMin();
                            syncMax();
                            document.forms['filterForm'].submit();
                        }

                        minSlider.addEventListener('input', syncMin);
                        maxSlider.addEventListener('input', syncMax);
                        
                        // Initial z-index setup
                        minSlider.style.zIndex = "20";
                        maxSlider.style.zIndex = "10";
                    </script>
                </form>
            </div>
        </aside>

        <!-- Main Content (Grid) -->
        <main class="pets-main">

            <!-- Breadcrumbs -->
            <div class="pets-breadcrumbs">
                <a href="{{ route('client.home') }}">Home</a>
                <span>/</span>
                <span class="active">Browse Pets</span>
                
                @if(request('type'))
                    <span>/</span> <span class="text-brand-medium">{{ request('type') }}s</span>
                @endif
                @if(request('breed'))
                    <span>/</span> <span class="text-brand-medium">{{ request('breed') }}</span>
                @endif
            </div>

            <!-- Header & Sort -->
            <div class="pets-header">
                <div class="pets-header-title">
                    <h2>Available Friends</h2>
                    <span>{{ $pets->total() }} results found</span>
                </div>
                
                <div class="pets-header-actions">
                    @auth
                    <div class="pets-recommend-count-wrapper">
                        <label for="recommendCount" class="recommend-label">Show Top :</label>
                        <input type="number" 
                            id="recommendCount" 
                            min="1" 
                            max="10" 
                            value="{{ $recommendCount ?? 5 }}" 
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                            oninput="if(this.value > 10) this.value = 10; if(this.value < 1 && this.value !== '') this.value = 1;"
                            class="pets-input-number">
                        <span class="recommend-label-sub">Recommendations</span>
                    </div>
                    @endauth

                    <div class="pets-sort-wrapper">
                        <label class="pets-sort-label">Sort by Price:</label>
                        <select id="sortPriceTrigger" class="pets-sort-select">
                            <option value="">Default</option>
                            <option value="asc" {{ request('sort_price') == 'asc' ? 'selected' : '' }}>Low to High</option>
                            <option value="desc" {{ request('sort_price') == 'desc' ? 'selected' : '' }}>High to Low</option>
                        </select>
                    </div>
                </div>
            </div>



            @if($pets->count() > 0)
                <div class="pets-grid">
                    @foreach($pets as $pet)
                        <div class="pets-card">
                            <div class="pets-card-image">
                                <a href="{{ $pet->isSold() ? '#' : route('client.pets.show', $pet->PetID) }}"
                                   class="{{ $pet->isSold() ? 'cursor-default' : '' }}">
                                    <img src="{{ asset($pet->ImageURL1 ?: 'image/default-pet.png') }}"
                                         alt="{{ $pet->PetName }}"
                                         class="{{ $pet->isSold() ? 'pets-card-image-sold' : '' }}">
                                </a>

                                {{-- Recommend Badge --}}
                                @if(isset($recommendedPets) && $recommendedPets->pluck('PetID')->contains($pet->PetID))
                                    @php
                                        $score = $pet->recommendation_score ?? 0;
                                        $percent = $score > 0 ? round($score * 100) : null;
                                    @endphp
                                    <span class="pets-card-recommend-badge">
                                        RECOMMEND @if($percent) {{ $percent }}% @endif
                                    </span>
                                @endif

                                {{-- Breed Badge --}}
                                <div class="pets-card-badge">{{ $pet->Breed }}</div>

                                {{-- Sold Overlay --}}
                                @if($pet->isSold())
                                    <div class="pets-card-sold-overlay">
                                        <span class="pets-card-sold-badge">Not Available</span>
                                    </div>
                                @endif
                            </div>

                            <div class="pets-card-content">
                                <h3>{{ $pet->PetName }}</h3>
                                <p class="pets-card-price">RM {{ number_format($pet->Price, 2) }}</p>
                                @if($pet->isSold())
                                    <button disabled class="pets-card-button">Sold Out</button>
                                @else
                                    <a href="{{ route('client.pets.show', $pet->PetID) }}" class="pets-card-button">View Details</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pets-pagination">
                    {{ $pets->links() }}
                </div>
            @else

                <div class="pets-empty">
                    <p>No pets found matching your criteria.</p>
                    <a href="{{ route('client.pets.index') }}">Clear all filters</a>
                </div>
            @endif

        </main>
    </div>
</div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>

<script>
let model;
let pets = [];

document.addEventListener('DOMContentLoaded', async () => {
    model = await mobilenet.load();
    const res = await fetch('/api/pets/images');
    pets = await res.json();
});

/* Modal */
function openImageModal() {
    document.getElementById('imageModal').style.display = 'block';
}
function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

/* Drag & Drop */
document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('dropZone');

    if (dropZone) {
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            handleFile(e.dataTransfer.files[0]);
        });
    }
});

/* Core Logic */
async function handleFile(file) {
    if (!file) return;

    // Check if it's an image
    if (!file.type.startsWith('image/')) {
        alert('Please select an actual image file.');
        return;
    }

    const img = document.getElementById('previewImage');
    const loading = document.getElementById('loading');
    
    // Clear previous results
    document.getElementById('results').innerHTML = '';
    
    img.src = URL.createObjectURL(file);
    img.hidden = false;

    loading.hidden = false;
    loading.innerHTML = 'Optimizing and analyzing image...';

    try {
        await img.decode();
        
        // Downscale large images using canvas to save memory and prevent failures
        const scaledCanvas = downscaleImage(img, 800);
        
        // 1. Detection Check (Classification)
        const predictions = await model.classify(scaledCanvas);
        
        // List of common animal-related keywords reported by MobileNet
        const animalKeywords = ['dog', 'cat', 'puppy', 'kitten', 'retriever', 'terrier', 'spitz', 'hound', 'poodle', 'spaniel', 'persian', 'siamese', 'tabby', 'animal', 'mammal', 'pet'];
        const isPetDetected = predictions.some(p => 
            animalKeywords.some(keyword => p.className.toLowerCase().includes(keyword))
        );

        // 2. Feature Extraction & Scoring
        const userFeatures = await extractFeatures(scaledCanvas);
        const scored = [];

        // Threshold of 45% (0.45)
        const THRESHOLD = 0.45;

        for (const pet of pets) {
            if (!pet.image_features) continue;
            
            const similarity = cosineSimilarity(userFeatures, pet.image_features);
            if (similarity >= THRESHOLD) {
                scored.push({ ...pet, similarity });
            }
        }

        scored.sort((a,b) => b.similarity - a.similarity);

        // 3. UI Display
        if (!isPetDetected && scored.length === 0) {
            loading.innerHTML = '<span style="color: #ef4444;">❌ No pets detected in this image. Please try a photo of a dog or cat!</span>';
            loading.hidden = false;
        } else {
            loading.hidden = true;
        }

        displayResults(scored.slice(0, 6));

    } catch (error) {
        console.error('Error during image search:', error);
        alert('Analysis failed. The image might be too large or corrupted for your browser to handle. Try a smaller version if possible.');
        loading.hidden = true;
    }
}

// Resizes image to a max dimension while maintaining aspect ratio
function downscaleImage(img, maxDim) {
    const canvas = document.createElement('canvas');
    let width = img.naturalWidth;
    let height = img.naturalHeight;

    if (width > height) {
        if (width > maxDim) {
            height *= maxDim / width;
            width = maxDim;
        }
    } else {
        if (height > maxDim) {
            width *= maxDim / height;
            height = maxDim;
        }
    }

    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, 0, width, height);
    return canvas;
}

async function extractFeatures(img) {
    // model.infer accepts HTMLCanvasElement and handles resizing and tensor conversion internally
    const features = model.infer(img, true);
    const data = await features.data();
    
    // Clean up tensors
    features.dispose();
    
    return data;
}

function cosineSimilarity(a, b) {
    let dot = 0, na = 0, nb = 0;
    for (let i = 0; i < a.length; i++) {
        dot += a[i] * b[i];
        na += a[i] * a[i];
        nb += b[i] * b[i];
    }
    return dot / (Math.sqrt(na) * Math.sqrt(nb));
}

function displayResults(list) {
    const container = document.getElementById('results');
    container.innerHTML = '';

    if (list.length === 0) {
        container.innerHTML = `
            <div class="no-results-message" style="grid-column: 1/-1; text-align: center; padding: 2rem; color: #5a2c2c; background: #faf7f6; border-radius: 1rem;">
                <p style="font-weight: 600; font-size: 1.125rem;">No similar pets found.</p>
                <p style="font-size: 0.875rem; opacity: 0.7;">Try a clearer photo of a pet!</p>
            </div>
        `;
        return;
    }

    list.forEach(p => {
        const similarityPercentage = Math.round(p.similarity * 100);
        container.innerHTML += `
            <a href="${p.url}" class="result-card">
                <div class="result-card-image-wrapper">
                    <img src="${p.images[0]}" alt="${p.name}">
                    <div class="result-match-badge">
                        ${similarityPercentage}% Match
                    </div>
                </div>
                <div class="result-card-info">
                    <h4>${p.name}</h4>
                    <p>${p.breed}</p>
                    <div class="price">RM ${parseFloat(p.price).toFixed(2)}</div>
                </div>
            </a>
        `;
    });
}
    
    

    // Link the standalone Sort dropdown to the main filter form
    document.getElementById('sortPriceTrigger').addEventListener('change', function() {
        const val = this.value;
        const hiddenInput = document.getElementById('hiddenSortPrice');
        if(hiddenInput) {
            hiddenInput.value = val;
            document.getElementById('filterForm').submit();
        }
    });

    // Link the standalone Recommendation Count to the main filter form
    const recommendInput = document.getElementById('recommendCount');
    if (recommendInput) {
        recommendInput.addEventListener('change', function() {
            let val = parseInt(this.value);
            if (isNaN(val) || val < 1) val = 1;
            if (val > 10) val = 10;
            this.value = val;
            
            const hiddenInput = document.getElementById('hiddenRecommendCount');
            if(hiddenInput) {
                hiddenInput.value = val;
                document.getElementById('filterForm').submit();
            }
        });
    }


   function deleteSearchHistory(id) {
        if (!confirm('Delete this search history?')) return;
        
        fetch(`/search-history/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fade out and remove the item
                const item = document.getElementById(`search-history-${id}`);
                if (item) {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.remove();
                        
                        // Check if no more items left, remove container
                        const remaining = document.querySelectorAll('.pets-search-card');
                        if (remaining.length === 0) {
                            const container = document.querySelector('.pets-search-history-cards');
                            if (container) container.remove();
                        }
                    }, 200);
                }
            }
        })
        .catch(error => {
            console.error('Error deleting search history:', error);
            alert('Failed to delete search history');
        });
    }

    function clearAllSearchHistory() {
        if (!confirm('Clear all search history?')) return;

        fetch('{{ route("client.search-history.clear") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('petsSearchHistoryWrapper');
                if (container) {
                    container.style.opacity = '0';
                    container.style.transform = 'scale(0.95)';
                    setTimeout(() => container.remove(), 200);
                }
            } else {
                alert(data.message || 'Failed to clear search history');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to clear search history');
        });
    }

    function deleteSearchHistory(id) {
        if (!confirm('Remove this search history item?')) return;

        fetch(`/search-history/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById(`search-history-${id}`);
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.remove();
                        
                        // Check if no more items left, remove container
                        const remaining = document.querySelectorAll('.pets-search-card');
                        if (remaining.length === 0) {
                            const container = document.getElementById('petsSearchHistoryWrapper');
                            if (container) container.remove();
                        }
                    }, 200);
                }
            } else {
                alert(data.message || 'Failed to delete search history item');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to delete search history item');
        });
    }
    function toggleSearchHistory() {
        const body = document.getElementById('petsSearchHistoryBody');
        const icon = document.getElementById('toggleIcon');
        
        if (!body || !icon) return;

        body.classList.toggle('hidden');
        
        // Change icon based on state
        if (body.classList.contains('hidden')) {
            icon.textContent = '+';
        } else {
            icon.textContent = '−';
        }
    }
</script>
@endsection
