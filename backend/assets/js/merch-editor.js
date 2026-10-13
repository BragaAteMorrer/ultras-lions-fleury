(() => {
    const editorRoot = document.querySelector('[data-merch-editor="1"]');
    if (!editorRoot) {
        return;
    }

    const form = editorRoot.querySelector('[data-merch-form="1"]');
    const titleInput = form?.querySelector('[name$="[title]"]');
    const categorySelect = form?.querySelector('[name$="[category]"]');
    const priceInput = form?.querySelector('[name$="[price]"]');
    const memberPriceInput = form?.querySelector('[name$="[memberPrice]"]');
    const audienceSelect = form?.querySelector('[name$="[audience]"]');
    const publicUntilInput = form?.querySelector('[name$="[publicUntil]"]');
    const imageSelect = form?.querySelector('[name$="[image]"]');
    const gadgetInput = form?.querySelector('[name$="[displayInGadgetHistory]"]');
    const descriptionTextarea = form?.querySelector('[name$="[description]"]');
    const titleCounterNode = editorRoot.querySelector('[data-merch-title-counter]');
    const titleFeedbackNode = editorRoot.querySelector('[data-merch-title-feedback]');
    const titlePreviewNode = editorRoot.querySelector('[data-merch-title-preview]');
    const categoryPreviewNode = editorRoot.querySelectorAll('[data-merch-category-preview]');
    const pricePreviewNodes = editorRoot.querySelectorAll('[data-merch-price-preview]');
    const memberPricePreviewNodes = editorRoot.querySelectorAll('[data-merch-member-price-preview]');
    const audiencePreviewNodes = editorRoot.querySelectorAll('[data-merch-audience-preview]');
    const publicUntilPreviewNodes = editorRoot.querySelectorAll('[data-merch-public-until-preview]');
    const stockCountNodes = editorRoot.querySelectorAll('[data-merch-stock-count]');
    const gadgetPreviewNodes = editorRoot.querySelectorAll('[data-merch-gadget-preview]');
    const imagePreviewNode = editorRoot.querySelector('[data-merch-image-preview]');
    const imagePathPreviewNode = editorRoot.querySelector('[data-merch-image-path-preview]');
    const imageVisualNode = editorRoot.querySelector('[data-merch-preview-visual]');
    const previewTitleNode = editorRoot.querySelector('[data-merch-preview-title]');
    const previewMetaNode = editorRoot.querySelector('[data-merch-preview-meta]');
    const previewDescriptionNode = editorRoot.querySelector('[data-merch-preview-description]');
    const descriptionStatsNode = editorRoot.querySelector('[data-merch-description-stats]');
    const stockCollection = editorRoot.querySelector('[data-merch-stock-collection]');
    const stockAddButton = editorRoot.querySelector('[data-merch-stock-add]');
    const stockEmptyNode = editorRoot.querySelector('[data-merch-stock-empty]');
    const stockPrototypeName = stockCollection?.dataset.prototypeName || '__size__';
    const mediaPicker = editorRoot.querySelector('[data-media-picker]');
    const mediaLibrary = mediaPicker?.querySelector('[data-media-library]');
    const mediaSearch = mediaPicker?.querySelector('[data-media-search]');
    const mediaSelectField = mediaPicker?.querySelector('[data-media-select="1"]');
    const mediaCards = Array.from(mediaPicker?.querySelectorAll('[data-media-card]') ?? []);

    if (!form || !titleInput || !categorySelect || !priceInput || !memberPriceInput || !audienceSelect || !imageSelect || !gadgetInput || !descriptionTextarea) {
        return;
    }

    let dirty = false;
    let stockPrototypeIndex = stockCollection ? stockCollection.querySelectorAll('[data-merch-stock-item]').length : 0;

    function setText(nodes, value) {
        nodes.forEach((node) => {
            node.textContent = value;
        });
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalizeSearch(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '');
    }

    function parsePrice(value) {
        const normalized = String(value || '')
            .replace(/\s/g, '')
            .replace(',', '.')
            .replace(/[^0-9.]/g, '');

        const numeric = Number.parseFloat(normalized);
        return Number.isFinite(numeric) ? numeric : null;
    }

    function formatPrice(value) {
        if (value === null) {
            return 'Non renseigne';
        }

        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
        }).format(value);
    }

    function getAudienceLabel() {
        return audienceSelect.value === 'members' ? 'Membres uniquement' : 'Tous les visiteurs';
    }

    function formatLocalDateTime(value) {
        if (!value) {
            return 'Aucune limite';
        }

        const parsed = new Date(value);
        if (Number.isNaN(parsed.getTime())) {
            return 'Aucune limite';
        }

        return new Intl.DateTimeFormat('fr-FR', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(parsed);
    }

    function getPublicUntilLabel() {
        return formatLocalDateTime(publicUntilInput?.value || '');
    }

    function getStockCountLabel() {
        const count = stockCollection?.querySelectorAll('[data-merch-stock-item]').length ?? 0;
        return count > 0 ? `${count} taille(s)` : 'Taille unique';
    }

    function getCategoryLabel() {
        return categorySelect.selectedOptions[0]?.textContent?.trim() || 'Aucune categorie';
    }

    function getGadgetLabel() {
        return gadgetInput.checked ? 'Oui' : 'Non';
    }

    function getSelectedImageOption() {
        return imageSelect.selectedOptions[0] ?? null;
    }

    function updateMediaPicker() {
        const query = normalizeSearch(mediaSearch?.value || '');

        mediaCards.forEach((card) => {
            if (card.dataset.mediaValue === '') {
                card.classList.remove('is-hidden');
                return;
            }

            const haystack = normalizeSearch(
                `${card.dataset.mediaLabel || ''} ${card.dataset.mediaPath || ''} ${card.dataset.mediaAlt || ''}`
            );
            card.classList.toggle('is-hidden', !(query === '' || haystack.includes(query)));
        });
    }

    function updateMediaSelection() {
        const selectedValue = imageSelect.value;

        mediaCards.forEach((card) => {
            const isSelected = card.dataset.mediaValue === selectedValue;
            card.classList.toggle('is-selected', isSelected);
            card.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
        });
    }

    function updateImagePreview() {
        const option = getSelectedImageOption();
        const path = option?.dataset.mediaPath || '';
        const label = option?.dataset.mediaLabel || option?.textContent?.trim() || 'Aucune image';
        const alt = option?.dataset.mediaAlt || label;
        const kind = option?.dataset.mediaKind || 'image';

        setText([imagePreviewNode].filter(Boolean), label);
        setText([imagePathPreviewNode].filter(Boolean), path || 'Aucun fichier associe');

        if (!imageVisualNode) {
            return;
        }

        if (!path) {
            imageVisualNode.innerHTML = '<div class="media-preview-card__empty">Aucune image selectionnee</div>';
            return;
        }

        const fileUrl = new URL(`/uploads/media/${path}`, window.location.origin).toString();
        if (kind === 'video') {
            imageVisualNode.innerHTML = `
                <video controls preload="metadata">
                    <source src="${escapeHtml(fileUrl)}">
                </video>
            `;
            return;
        }

        imageVisualNode.innerHTML = `<img src="${escapeHtml(fileUrl)}" alt="${escapeHtml(alt)}">`;
    }

    function updateStockSummary() {
        const countLabel = getStockCountLabel();
        setText(stockCountNodes, countLabel);

        if (stockEmptyNode) {
            stockEmptyNode.classList.toggle('d-none', (stockCollection?.querySelectorAll('[data-merch-stock-item]').length ?? 0) > 0);
        }
    }

    function bindStockItem(item) {
        const removeButton = item.querySelector('[data-merch-stock-remove]');
        removeButton?.addEventListener('click', () => {
            item.remove();
            updateStockSummary();
            markDirty();
        });
    }

    function createStockItem() {
        if (!stockCollection?.dataset.prototype) {
            return null;
        }

        const template = document.createElement('template');
        const html = stockCollection.dataset.prototype.split(stockPrototypeName).join(String(stockPrototypeIndex++));
        template.innerHTML = html.trim();
        const item = template.content.firstElementChild;
        if (item) {
            bindStockItem(item);
        }

        return item;
    }

    function updateSummary() {
        const title = titleInput.value.trim() || 'Titre du produit';
        const category = getCategoryLabel();
        const price = formatPrice(parsePrice(priceInput.value));
        const memberPrice = formatPrice(parsePrice(memberPriceInput.value));
        const audience = getAudienceLabel();
        const publicUntil = getPublicUntilLabel();
        const gadget = getGadgetLabel();
        const text = descriptionTextarea.value
            .replace(/<[^>]*>/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
        const words = text === '' ? 0 : text.split(/\s+/).filter(Boolean).length;

        if (titleCounterNode) {
            titleCounterNode.textContent = `${titleInput.value.trim().length}/180 caracteres`;
        }

        if (titleFeedbackNode) {
            titleFeedbackNode.textContent = titleInput.value.trim() === '' ? 'Titre requis' : 'Titre pret';
        }

        setText([titlePreviewNode].filter(Boolean), title);
        setText(categoryPreviewNode, category);
        setText(pricePreviewNodes, price);
        setText(memberPricePreviewNodes, memberPrice);
        setText(audiencePreviewNodes, audience);
        setText(publicUntilPreviewNodes, publicUntil);
        setText(gadgetPreviewNodes, gadget);
        setText(stockCountNodes, getStockCountLabel());

        if (previewTitleNode) {
            previewTitleNode.textContent = title;
        }

        if (previewMetaNode) {
            previewMetaNode.textContent = `${price} · ${audience}`;
        }

        if (previewDescriptionNode) {
            previewDescriptionNode.textContent = text || 'Description du produit';
        }

        if (descriptionStatsNode) {
            descriptionStatsNode.textContent = `${words} mot${words > 1 ? 's' : ''}`;
        }

        updateImagePreview();
        updateMediaSelection();
    }

    function markDirty() {
        dirty = true;
    }

    function clearDirty() {
        dirty = false;
    }

    [titleInput, categorySelect, priceInput, memberPriceInput, audienceSelect, publicUntilInput, imageSelect, gadgetInput, descriptionTextarea].forEach((field) => {
        field?.addEventListener('input', () => {
            updateSummary();
            markDirty();
        });

        field?.addEventListener('change', () => {
            updateSummary();
            markDirty();
        });
    });

    stockCollection?.querySelectorAll('[data-merch-stock-item]').forEach((item) => {
        bindStockItem(item);
    });

    stockAddButton?.addEventListener('click', () => {
        const item = createStockItem();
        if (!item || !stockCollection) {
            return;
        }

        stockCollection.appendChild(item);
        updateStockSummary();
        updateSummary();
        markDirty();
    });

    mediaSearch?.addEventListener('input', () => {
        updateMediaPicker();
    });

    mediaLibrary?.addEventListener('click', (event) => {
        const card = event.target instanceof Element ? event.target.closest('[data-media-card]') : null;
        if (!card || !mediaLibrary?.contains(card) || !mediaSelectField) {
            return;
        }

        const targetValue = String(card.dataset.mediaValue || '');
        Array.from(imageSelect.options).forEach((option) => {
            option.selected = option.value === targetValue;
        });
        imageSelect.value = targetValue;
        try {
            imageSelect.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (error) {
            // Keep the UI responsive even if the browser blocks the synthetic event.
        }
        updateSummary();
        markDirty();
    });

    imageSelect?.addEventListener('change', () => {
        updateSummary();
        updateMediaPicker();
        updateMediaSelection();
        markDirty();
    });

    form.addEventListener('submit', () => {
        clearDirty();
    });

    window.addEventListener('beforeunload', (event) => {
        if (!dirty) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    });

    updateMediaPicker();
    updateStockSummary();
    updateSummary();
})();
