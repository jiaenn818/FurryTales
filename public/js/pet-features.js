let mobilenetModel;

async function loadMobileNetModel() {
    if (!mobilenetModel) {
        console.log('Loading MobileNet model...');
        mobilenetModel = await mobilenet.load();
    }
    return mobilenetModel;
}

// Utility to load an image from URL or File object
function loadPetImage(source) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => resolve(img);
        img.onerror = reject;
        if (source instanceof File) {
            img.src = URL.createObjectURL(source);
        } else {
            img.src = source;
        }
    });
}

/**
 * Generates an averaged feature vector from a list of images.
 * @param {Array<File|String>} images - List of File objects or Image URLs
 * @returns {Promise<Array<number>|null>} - The averaged feature vector
 */
async function generatePetFeatures(images) {
    try {
        const model = await loadMobileNetModel();
        let accumulatedFeatures = new Float32Array(1024).fill(0);
        let validImageCount = 0;

        for (const imageSource of images) {
            if (!imageSource) continue;
            try {
                const img = await loadPetImage(imageSource);
                const featureVector = await model.infer(img, true);
                const featuresArray = await featureVector.data();

                for (let i = 0; i < featuresArray.length; i++) {
                    accumulatedFeatures[i] += featuresArray[i];
                }
                validImageCount++;

                featureVector.dispose();
            } catch (err) {
                console.warn('Failed to process one of the images:', err);
            }
        }

        if (validImageCount > 0) {
            for (let i = 0; i < accumulatedFeatures.length; i++) {
                accumulatedFeatures[i] /= validImageCount;
            }
            return Array.from(accumulatedFeatures);
        }
    } catch (err) {
        console.error('Feature generation failed:', err);
    }
    return null;
}