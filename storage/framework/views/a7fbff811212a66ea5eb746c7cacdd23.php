<!DOCTYPE html>
<html>
<head>
    <title>Generate Pet Features</title>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>
<body>
    <h2>Pet Features Generator</h2>
    <p>This page will generate and replace image features for ALL pets.</p>
    <button onclick="generateAllFeatures()">Regenerate All Features</button>
    <div id="progress"></div>
    <div id="results"></div>

    <script>
        let model;

        async function generateAllFeatures() {
            try {
                if (!model) {
                    document.getElementById('progress').innerHTML = 'Loading model...';
                    model = await mobilenet.load();
                }

                document.getElementById('progress').innerHTML = 'Fetching pets...';
                
                // Fetch all pets from your API
                const response = await fetch('/api/pets/images');
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Server error (${response.status}): ${errorText.substring(0, 100)}`);
                }
                const allPets = await response.json();
                
                // Process ALL pets to force update
                const pets = allPets;

                if (pets.length === 0) {
                    document.getElementById('progress').innerHTML = '✅ No pets found in database!';
                    return;
                }

                const features = [];
                
                for (let i = 0; i < pets.length; i++) {
                    const pet = pets[i];
                    document.getElementById('progress').innerHTML = 
                        `Processing ${i + 1}/${pets.length}: ${pet.name}`;

                    try {
                        const petImages = pet.images && pet.images.length > 0 ? pet.images : (pet.image ? [pet.image] : []);
                        
                        if (petImages.length === 0) {
                            console.warn(`No images for ${pet.name}`);
                            continue;
                        }

                        // MobileNet feature vector size is 1024
                        let accumulatedFeatures = new Float32Array(1024).fill(0);
                        let validImageCount = 0;

                        for (const imageUrl of petImages) {
                            try {
                                const img = await loadImage(imageUrl);
                                const featureVector = await model.infer(img, true);
                                const featuresArray = await featureVector.data();
                                
                                // Accumulate features
                                for (let j = 0; j < featuresArray.length; j++) {
                                    accumulatedFeatures[j] += featuresArray[j];
                                }
                                validImageCount++;
                                
                                // Clean up tensor
                                featureVector.dispose();
                            } catch (imgError) {
                                console.warn(`Failed to process image for ${pet.name}: ${imageUrl}`, imgError);
                            }
                        }

                        if (validImageCount > 0) {
                            // Calculate average
                            for (let j = 0; j < accumulatedFeatures.length; j++) {
                                accumulatedFeatures[j] /= validImageCount;
                            }

                            features.push({
                                pet_id: pet.id,
                                features: Array.from(accumulatedFeatures)
                            });
                        }

                    } catch (error) {
                        console.error(`Error processing ${pet.name}:`, error);
                    }
                    
                    // Save in batches of 10 to avoid huge payloads
                    if (features.length >= 10 || i === pets.length - 1) {
                        if (features.length > 0) {
                             await fetch('/api/save-pet-features', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ features: [...features] })
                            });
                            features.length = 0; // Clear the batch
                        }
                    }
                }

                document.getElementById('results').innerHTML = 
                    `✅ Generated features for ${allPets.length} pets!`;
            } catch (error) {
                console.error('Feature generation failed:', error);
                document.getElementById('progress').innerHTML = `❌ Error: ${error.message}`;
                alert('Failed to generate features. Check the console for details.');
            }
        }

        function loadImage(src) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = src;
            });
        }
    </script>
</body>
</html><?php /**PATH C:\Users\User\finalyear\resources\views/generate_features.blade.php ENDPATH**/ ?>