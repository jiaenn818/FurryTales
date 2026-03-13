

<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">


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
                <form id="filterForm" action="<?php echo e(route('pets.index')); ?>" method="GET">
                    
                    
                    <input type="hidden" name="sort_price" id="hiddenSortPrice" value="<?php echo e(request('sort_price')); ?>">
                    <input type="hidden" name="type" value="<?php echo e(request('type')); ?>">
                    <input type="hidden" name="breed" value="<?php echo e(request('breed')); ?>">
                    <input type="hidden" name="recommend_count" id="hiddenRecommendCount" value="<?php echo e($recommendCount ?? 5); ?>">


                    <div class="pets-search-wrapper">
                        <input type="text"
                            name="search"
                            placeholder="Search pets..."
                            value="<?php echo e(request('search')); ?>"
                            class="pets-search-input"
                            autocomplete="off">

                        <button type="submit" class="pets-search-button">
                            🔍
                        </button>
                    </div>

                    
                    <?php if(isset($searchHistories) && $searchHistories->count()): ?>
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
                            <?php $__currentLoopData = $searchHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="pets-search-card" id="search-history-<?php echo e($history->SearchHistoryID); ?>">
                                    <a href="<?php echo e(route('pets.index', ['search' => $history->keyword])); ?>"
                                    class="pets-search-link">
                                        <?php echo e($history->keyword); ?>

                                    </a>
                                    <button type="button"
                                            onclick="deleteSearchHistory(<?php echo e($history->SearchHistoryID); ?>)"
                                            class="pets-search-card-delete">
                                        ×
                                    </button>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                    </div>
                    <?php endif; ?>

                    <button type="button" class="pets-image-search-button" onclick="openImageModal()">
                        📸 Search by Photo
                    </button>

                    <!-- Categories -->
                    <?php $__currentLoopData = ['Cat', 'Dog']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="pets-category-section">
                            <?php
                                $newParams = request()->query();
                                $newParams['type'] = $type;
                                unset($newParams['breed'], $newParams['search']);
                                $isActiveType = request('type') === $type;
                            ?>


                            <h4 class="pets-category-header">
                                <a href="<?php echo e(route('pets.index', $newParams)); ?>" 
                                   class="pets-category-title <?php echo e($isActiveType ? 'active' : ''); ?>">
                                   <?php echo e($type); ?>s
                                </a>
                                <span class="pets-category-badge"><?php echo e(isset($breeds[$type]) ? count($breeds[$type]) : 0); ?></span>
                            </h4>

                            <ul class="pets-breed-list">
                                
                                <?php
                                    $allParams = request()->query();
                                    unset($allParams['breed'], $allParams['type'], $allParams['search']);
                                ?>

                                <li class="pets-breed-item">
                                    <a href="<?php echo e(route('pets.index', $allParams)); ?>"
                                       class="<?php echo e(!request('type') ? 'active' : ''); ?>">
                                       Show All
                                    </a>
                                </li>

                                
                                <?php if(isset($breeds[$type])): ?>
                                    <?php $__currentLoopData = $breeds[$type]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $params = request()->query();
                                            $params['type'] = $type;
                                            $params['breed'] = $breed->Breed;
                                            unset($params['search']);
                                        ?>

                                        <li class="pets-breed-item">
                                            <a href="<?php echo e(route('pets.index', $params)); ?>"
                                               class="<?php echo e(request('breed') === $breed->Breed ? 'active' : ''); ?>">
                                               <?php echo e($breed->Breed); ?>

                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                    <!-- Outlets -->
                    <div class="pets-category-section">
                        <h4 class="pets-category-header">
                            <span class="pets-category-title">Outlet</span>
                        </h4>
                        <select name="outlet" onchange="this.form.submit()" class="pets-select">
                            <option value="">All Outlets</option>
                            <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($outlet->OutletID); ?>"
                                    <?php echo e(request('outlet') === $outlet->OutletID ? 'selected' : ''); ?>>
                                    <?php echo e($outlet->State); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        
                        <?php
                            $min = request('min_price') ?? ($dbMinPrice ?? 0);
                            $max = request('max_price') ?? ($dbMaxPrice ?? 9999);
                            $dbMin = $dbMinPrice ?? 0;
                            $dbMax = $dbMaxPrice ?? 9999;
                        ?>

                        <div class="pets-price-display">
                            <span>RM <span id="minPriceVal"><?php echo e($min); ?></span></span>
                            <span>RM <span id="maxPriceVal"><?php echo e($max); ?></span></span>
                        </div>

                        <div class="pets-price-slider-wrapper">
                            <!-- Track -->
                            <div class="pets-price-track"></div>

                            <!-- Sliders -->
                            <input type="range" id="minPrice" name="min_price" min="<?php echo e($dbMin); ?>" max="<?php echo e($dbMax); ?>" value="<?php echo e($min); ?>" 
                                class="pets-price-slider">
                            <input type="range" id="maxPrice" name="max_price" min="<?php echo e($dbMin); ?>" max="<?php echo e($dbMax); ?>" value="<?php echo e($max); ?>" 
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
                        const dbMin = <?php echo e($dbMin); ?>;
                        const dbMax = <?php echo e($dbMax); ?>;

                        function syncMin() {
                            if (+minSlider.value > +maxSlider.value) minSlider.value = maxSlider.value;
                            minVal.innerText = minSlider.value;
                        }
                        function syncMax() {
                            if (+maxSlider.value < +minSlider.value) maxSlider.value = minSlider.value;
                            maxVal.innerText = maxSlider.value;
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
                    </script>
                </form>
            </div>
        </aside>

        <!-- Main Content (Grid) -->
        <main class="pets-main">

            <!-- Breadcrumbs -->
            <div class="pets-breadcrumbs">
                <a href="<?php echo e(route('main.page')); ?>">Home</a>
                <span>/</span>
                <span class="active">Browse Pets</span>
                
                <?php if(request('type')): ?>
                    <span>/</span> <span class="text-brand-medium"><?php echo e(request('type')); ?>s</span>
                <?php endif; ?>
                <?php if(request('breed')): ?>
                    <span>/</span> <span class="text-brand-medium"><?php echo e(request('breed')); ?></span>
                <?php endif; ?>
            </div>

            <!-- Header & Sort -->
            <div class="pets-header">
                <div class="pets-header-title">
                    <h2>Available Friends</h2>
                    <span><?php echo e($pets->total()); ?> results found</span>
                </div>
                
                <div class="pets-header-actions">
                    <?php if(auth()->guard()->check()): ?>
                    <div class="pets-recommend-count-wrapper">
                        <label for="recommendCount" class="recommend-label">Show Top :</label>
                        <input type="number" 
                            id="recommendCount" 
                            min="1" 
                            max="10" 
                            value="<?php echo e($recommendCount ?? 5); ?>" 
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                            oninput="if(this.value > 10) this.value = 10; if(this.value < 1 && this.value !== '') this.value = 1;"
                            class="pets-input-number">
                        <span class="recommend-label-sub">Recommendations</span>
                    </div>
                    <?php endif; ?>

                    <div class="pets-sort-wrapper">
                        <label class="pets-sort-label">Sort by Price:</label>
                        <select id="sortPriceTrigger" class="pets-sort-select">
                            <option value="">Default</option>
                            <option value="asc" <?php echo e(request('sort_price') == 'asc' ? 'selected' : ''); ?>>Low to High</option>
                            <option value="desc" <?php echo e(request('sort_price') == 'desc' ? 'selected' : ''); ?>>High to Low</option>
                        </select>
                    </div>
                </div>
            </div>

            <?php if($pets->count() > 0): ?>
                <div class="pets-grid">
                    <?php $__currentLoopData = $pets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="pets-card">
                            <div class="pets-card-image">
                                <a href="<?php echo e($pet->isSold() ? '#' : route('pets.show', $pet->PetID)); ?>"
                                   class="<?php echo e($pet->isSold() ? 'cursor-default' : ''); ?>">
                                    <img src="<?php echo e(asset($pet->ImageURL1 ?: 'image/default-pet.png')); ?>"
                                         alt="<?php echo e($pet->PetName); ?>"
                                         class="<?php echo e($pet->isSold() ? 'pets-card-image-sold' : ''); ?>">
                                </a>

                                
                                <?php if(isset($recommendedPets) && $recommendedPets->pluck('PetID')->contains($pet->PetID)): ?>
                                    <?php
                                        $score = $pet->recommendation_score ?? 0;
                                        $percent = $score > 0 ? round($score * 100) : null;
                                    ?>
                                    <span class="pets-card-recommend-badge">
                                        RECOMMEND <?php if($percent): ?> <?php echo e($percent); ?>% <?php endif; ?>
                                    </span>
                                <?php endif; ?>

                                
                                <div class="pets-card-badge"><?php echo e($pet->Breed); ?></div>

                                
                                <?php if($pet->isSold()): ?>
                                    <div class="pets-card-sold-overlay">
                                        <span class="pets-card-sold-badge">Not Available</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="pets-card-content">
                                <h3><?php echo e($pet->PetName); ?></h3>
                                <p class="pets-card-price">RM <?php echo e(number_format($pet->Price, 2)); ?></p>
                                <?php if($pet->isSold()): ?>
                                    <button disabled class="pets-card-button">Sold Out</button>
                                <?php else: ?>
                                    <a href="<?php echo e(route('pets.show', $pet->PetID)); ?>" class="pets-card-button">View Details</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Pagination -->
                <div class="pets-pagination">
                    <?php echo e($pets->links()); ?>

                </div>
            <?php else: ?>

                <div class="pets-empty">
                    <p>No pets found matching your criteria.</p>
                    <a href="<?php echo e(route('pets.index')); ?>">Clear all filters</a>
                </div>
            <?php endif; ?>

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
    const tensor = tf.browser
        .fromPixels(img)
        .resizeBilinear([224,224])
        .toFloat()
        .expandDims();

    const features = model.infer(tensor, true);
    const data = await features.data();
    
    // Clean up tensors
    tensor.dispose();
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
    
// Fetch and display recommendations
document.addEventListener('DOMContentLoaded', function() {
    <?php if(auth()->guard()->check()): ?>
    const grid = document.getElementById('recommendationsGrid');
    if (!grid) return; // Exit if the section doesn't exist (e.g., on Page 2)

    fetch('/api/recommendations')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.recommendations && data.recommendations.length > 0) {
                const section = document.getElementById('recommendationsSection');
                const count = document.getElementById('recommendationsCount');
                
                if (!section) return;
                
                // Show the section
                section.style.display = 'block';
                count.textContent = `${data.recommendations.length} pets recommended for you`;
                
                // For each recommended pet, fetch details and create card
                data.recommendations.forEach(rec => {
                    fetch(`/api/pets/${rec.PetID}`)
                        .then(res => res.json())
                        .then(pet => {
                            const card = document.createElement('div');
                            card.className = 'pets-card';
                            
                            const isSold = pet.isSold || false;
                            
                            card.innerHTML = `
                                <div class="pets-card-image">
                                    <a href="${isSold ? '#' : '/pets/' + pet.PetID}" class="${isSold ? 'cursor-default' : ''}">
                                        <img src="${pet.ImageURL1 || '/image/default-pet.png'}" alt="${pet.PetName}" class="${isSold ? 'pets-card-image-sold' : ''}">
                                    </a>
                                    
                                    <!-- Recommend Badge -->
                                    <div class="pets-card-badge-recommend">
                                        RECOMMEND ${rec.final_score ? Math.round(rec.final_score * 100) + '%' : ''}
                                    </div>
                                    
                                    <!-- Breed Badge -->
                                    <div class="pets-card-badge">${pet.Breed}</div>
                                    
                                    ${isSold ? '<div class="pets-card-sold-overlay"><span class="pets-card-sold-badge">Not Available</span></div>' : ''}
                                </div>
                                
                                <div class="pets-card-content">
                                    <h3>${pet.PetName}</h3>
                                    <p class="pets-card-price">RM ${parseFloat(pet.Price).toFixed(2)}</p>
                                    ${isSold 
                                        ? '<button disabled class="pets-card-button">Sold Out</button>'
                                        : `<a href="/pets/${pet.PetID}" class="pets-card-button">View Details</a>`}
                                </div>
                            `;
                            
                            grid.appendChild(card);
                        })
                        .catch(err => console.error('Error fetching pet details:', err));
                });
            }
        })
        .catch(err => console.error('Error fetching recommendations:', err));
    <?php endif; ?>
});

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
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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

        fetch('<?php echo e(route("search-history.clear")); ?>', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/pets/index.blade.php ENDPATH**/ ?>