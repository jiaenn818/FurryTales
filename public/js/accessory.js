document.addEventListener('DOMContentLoaded', function () {
    // -------------------- Tabs --------------------
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const target = tab.dataset.tab;
            contents.forEach(c => c.classList.remove('active'));
            const targetEl = document.getElementById(target);
            if (targetEl) targetEl.classList.add('active');
        });
    });

    // -------------------- Image Preview --------------------
    window.previewImage = function (input, index) {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = document.getElementById('preview-' + index);
            const uploadBox = document.getElementById('uploadBox-' + index);
            const placeholder = uploadBox?.querySelector('.upload-placeholder');
            const removeBtn = uploadBox?.querySelector('.remove-image');

            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            if (placeholder) placeholder.style.display = 'none';
            if (removeBtn) removeBtn.style.display = 'flex';

            // Update upload box background
            if (uploadBox) {
                uploadBox.style.backgroundImage = `url(${e.target.result})`;
                uploadBox.style.backgroundSize = 'cover';
                uploadBox.style.backgroundPosition = 'center';
            }
        };
        reader.readAsDataURL(file);
    };

    window.removeImage = function (index) {
        const input = document.getElementById('image-' + index);
        const preview = document.getElementById('preview-' + index);
        const uploadBox = document.getElementById('uploadBox-' + index);
        const placeholder = uploadBox?.querySelector('.upload-placeholder');
        const removeBtn = uploadBox?.querySelector('.remove-image');

        if (input) input.value = '';
        if (preview) {
            preview.src = '';
            preview.style.display = 'none';
        }
        if (placeholder) placeholder.style.display = 'flex';
        if (removeBtn) removeBtn.style.display = 'none';

        if (uploadBox) {
            uploadBox.style.backgroundImage = '';
            uploadBox.style.backgroundColor = '';
        }
    };



    // -------------------- Variants --------------------
    window.addVariant = function () {

        const variantList = document.getElementById('variant-list');
        if (!variantList) {
            console.warn('variant-list not found');
            return;
        }

        // Always calculate index from DOM (safer than variantCount)
        const variants = variantList.querySelectorAll('.variant-item');
        const newIndex = variants.length;

        console.log('addVariant clicked, newIndex =', newIndex);

        // Build fresh HTML (no cloning dependency)
        const html = `
        <div class="variant-item" data-index="${newIndex}">
            <div class="variant-header">
                <h4>Variant #${newIndex + 1}</h4>
                <button type="button" class="remove-variant" onclick="removeVariant(this)">Remove</button>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Variant Key</label>
                    <input type="text" name="variants[${newIndex}][VariantKey]" class="form-control" value="">
                </div>
                <div class="form-group">
                    <label>Price (RM)</label>
                    <input type="number" step="0.01" name="variants[${newIndex}][Price]" class="form-control" value="0">
                </div>
            </div>

            <div class="outlet-stock-section">
                <h5>Outlet Stock</h5>
                <div class="outlet-stock-list"></div>
                <button type="button" class="btn btn-primary" onclick="addOutletStock(this)">Add Outlet Stock</button>
            </div>
        </div>
    `;

        variantList.insertAdjacentHTML('beforeend', html);
    };

    window.removeVariant = function (button) {
        const variantItem = button.closest('.variant-item');
        if (!variantItem) return;

        const allVariants = document.querySelectorAll('.variant-item');
        if (allVariants.length > 1) {
            variantItem.remove();
            updateVariantHeaders();
        } else {
            alert("At least one variant is required.");
        }
    };

    function updateVariantHeaders() {
        document.querySelectorAll('.variant-item').forEach((item, index) => {
            item.dataset.index = index;
            const header = item.querySelector('.variant-header h4');
            if (header) header.textContent = `Variant #${index + 1}`;

            item.querySelectorAll('.outlet-stock-item').forEach((stockItem, stockIndex) => {
                stockItem.dataset.index = stockIndex;
                stockItem.querySelectorAll('input, select').forEach(el => {
                    if (el.name) {
                        el.name = el.name.replace(/variants\[\d+\]/, `variants[${index}]`)
                            .replace(/outlets\[\d+\]/, `outlets[${stockIndex}]`);
                    }
                });
            });
        });
    }

    // -------------------- Outlet Stock --------------------
    window.addOutletStock = function (button) {
        const section = button.closest('.outlet-stock-section');
        const list = section?.querySelector('.outlet-stock-list');
        if (!list) return;

        const items = list.querySelectorAll('.outlet-stock-item');
        const index = items.length;

        const variantItem = button.closest('.variant-item');
        const variantIndex = variantItem?.dataset.index ?? 0;

        const templateEl = document.getElementById('outletStockTemplate');
        if (!templateEl) return;

        let template = templateEl.innerHTML;
        template = template.replace(/__INDEX__/g, index).replace(/__VARIANT__/g, variantIndex);

        const div = document.createElement('div');
        div.innerHTML = template;
        list.appendChild(div.firstElementChild);
    };

    window.removeOutletStock = function (button) {
        const stockItem = button.closest('.outlet-stock-item');
        if (stockItem) stockItem.remove();
    };
});