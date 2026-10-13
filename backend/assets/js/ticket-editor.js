(() => {
    function initTicketEditor() {
        const editorRoot = document.querySelector('[data-ticket-editor="1"]');
        if (!editorRoot || editorRoot.dataset.ticketEditorReady === '1') {
            return;
        }

        editorRoot.dataset.ticketEditorReady = '1';

    const form = editorRoot.querySelector('[data-ticket-form="1"]');
    const titleInput = form?.querySelector('[name$="[title]"]');
    const opponentInput = form?.querySelector('[name$="[opponent]"]');
    const matchDateInput = form?.querySelector('[name$="[matchDate]"]');
    const matchLocationSelect = form?.querySelector('[name$="[matchLocation]"]');
    const categorySelect = form?.querySelector('[name$="[category]"]');
    const priceInput = form?.querySelector('[name$="[price]"]');
    const stockInput = form?.querySelector('[name$="[stock]"]');
    const imageSelect = form?.querySelector('[name$="[image]"]');
    const imageIdField = form?.querySelector('[data-ticket-image-id]');
    const mediaSelect = imageSelect;
    const importTrigger = editorRoot.querySelector('[data-ticket-import-trigger]');
    const importInput = editorRoot.querySelector('[data-ticket-import-input]');
    const mediaPicker = editorRoot.querySelector('[data-media-picker]');
    const mediaLibrary = mediaPicker?.querySelector('[data-media-library]');
    const mediaSearch = mediaPicker?.querySelector('[data-media-search]');
    const mediaPreview = editorRoot.querySelector('[data-ticket-media-preview]');
    const mediaClearButton = editorRoot.querySelector('[data-ticket-media-clear]');
    let mediaCards = Array.from(mediaPicker?.querySelectorAll('[data-media-card]') ?? []);

    const matchPreviewNodes = editorRoot.querySelectorAll('[data-ticket-match-preview]');
    const datePreviewNodes = editorRoot.querySelectorAll('[data-ticket-date-preview]');
    const pricePreviewNodes = editorRoot.querySelectorAll('[data-ticket-price-preview]');
    const imageLabelNodes = editorRoot.querySelectorAll('[data-ticket-image-label]');
    const imagePathNodes = editorRoot.querySelectorAll('[data-ticket-image-path]');
    const titlePreviewNodes = editorRoot.querySelectorAll('[data-ticket-title-preview]');
    const titleCounterNodes = editorRoot.querySelectorAll('[data-ticket-title-counter]');
    const titleFeedbackNode = editorRoot.querySelector('[data-ticket-title-feedback]');
    const matchLabelNode = editorRoot.querySelector('[data-ticket-match-label]');
    const stockPreviewNode = editorRoot.querySelector('[data-ticket-stock-preview]');
    const accessPreviewNodes = editorRoot.querySelectorAll('[data-ticket-access-preview]');
    const openToGuestsInput = form?.querySelector('[name$="[openToGuests]"]');
    const guestAvailabilityField = editorRoot.querySelector('[data-ticket-guest-availability-field]');
    const guestAvailabilityInput = form?.querySelector('[name$="[guestAvailabilityDate]"]');

    if (!form || !titleInput || !opponentInput || !matchDateInput || !matchLocationSelect || !categorySelect || !priceInput || !stockInput || !imageSelect) {
        return;
    }

    let dirty = false;

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

    function formatPrice(value) {
        if (!Number.isFinite(value)) {
            return 'Prix non renseigne';
        }

        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
        }).format(value);
    }

    function parsePrice(value) {
        const normalized = String(value || '')
            .replace(/\s/g, '')
            .replace(',', '.')
            .replace(/[^0-9.]/g, '');

        const numeric = Number.parseFloat(normalized);
        return Number.isFinite(numeric) ? numeric : null;
    }

    function formatDateTime(value) {
        if (!value) {
            return 'Aucune date';
        }

        const parsed = new Date(value);
        if (Number.isNaN(parsed.getTime())) {
            return 'Aucune date';
        }

        return new Intl.DateTimeFormat('fr-FR', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(parsed);
    }

    function isAwayMatch() {
        const location = matchLocationSelect.value || '';
        if (location.toLowerCase() === 'exterieur') {
            return true;
        }

        const categorySlug = categorySelect.selectedOptions[0]?.dataset.categorySlug || '';
        return location === '' && categorySlug === 'ext';
    }

    function getMatchLabel() {
        const opponent = opponentInput.value.trim();
        if (opponent === '') {
            return titleInput.value.trim() || 'Billet sans titre';
        }

        return isAwayMatch() ? `${opponent} vs FC Fleury` : `FC Fleury vs ${opponent}`;
    }

    function getSelectedOption() {
        return imageSelect.selectedOptions[0] ?? null;
    }

    function updateImageSelection() {
        const selectedValue = imageSelect.value;
        mediaCards.forEach((card) => {
            const isSelected = card.dataset.mediaValue === selectedValue;
            card.classList.toggle('is-selected', isSelected);
            card.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
        });
    }

    function refreshMediaCards() {
        mediaCards = Array.from(mediaPicker?.querySelectorAll('[data-media-card]') ?? []);
    }

    function updateMediaFilter() {
        if (!mediaLibrary) {
            return;
        }

        refreshMediaCards();
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

    function updateImagePreview() {
        const option = getSelectedOption();
        const path = option?.dataset.mediaPath || '';
        const alt = option?.dataset.mediaAlt || option?.textContent?.trim() || 'Image principale';
        const kind = option?.dataset.mediaKind || 'image';

        setText(imageLabelNodes, option?.dataset.mediaLabel || option?.textContent?.trim() || 'Aucune image principale');
        setText(imagePathNodes, path || 'Aucun fichier associe');

        if (!mediaPreview) {
            return;
        }

        if (!path) {
            mediaPreview.innerHTML = '<div class="admin-media-preview__empty">Aucune image principale selectionnee</div>';
            return;
        }

        const fileUrl = new URL(`/uploads/media/${path}`, window.location.origin).toString();
        if (kind === 'video') {
            mediaPreview.innerHTML = `
                <video controls preload="metadata">
                    <source src="${escapeHtml(fileUrl)}">
                </video>
                <div>
                    <strong>${escapeHtml(alt)}</strong>
                    <small>${escapeHtml(path)}</small>
                </div>
            `;
        } else {
            mediaPreview.innerHTML = `
                <img src="${escapeHtml(fileUrl)}" alt="${escapeHtml(alt)}" loading="lazy">
                <div>
                    <strong>${escapeHtml(alt)}</strong>
                    <small>${escapeHtml(path)}</small>
                </div>
            `;
        }
    }

    function updateSummary() {
        const title = titleInput.value.trim() || 'Titre du billet';
        const opponent = opponentInput.value.trim();
        const matchLabel = getMatchLabel();
        const dateLabel = formatDateTime(matchDateInput.value);
        const priceLabel = formatPrice(parsePrice(priceInput.value) ?? NaN);
        const stockValue = Number.parseInt(stockInput.value || '0', 10);
        const stockLabel = Number.isFinite(stockValue) && stockValue > 0 ? `${stockValue} place(s)` : 'Complet';
        const titleLength = titleInput.value.trim().length;

        setText(matchPreviewNodes, matchLabel);
        setText(datePreviewNodes, dateLabel);
        setText(pricePreviewNodes, priceLabel);
        setText(titlePreviewNodes, title);
        setText(imageLabelNodes, getSelectedOption()?.dataset.mediaLabel || getSelectedOption()?.textContent?.trim() || 'Aucune image principale');
        setText(imagePathNodes, getSelectedOption()?.dataset.mediaPath || 'Aucun fichier associe');

        if (titleCounterNodes.length > 0) {
            setText(titleCounterNodes, `${titleLength}/180 caracteres`);
        }

        if (titleFeedbackNode) {
            titleFeedbackNode.textContent = titleLength === 0 ? 'Titre requis' : 'Titre pret';
        }

        if (matchLabelNode) {
            matchLabelNode.textContent = matchLabel;
        }

        if (stockPreviewNode) {
            stockPreviewNode.textContent = stockLabel;
        }

        const accessLabel = openToGuestsInput?.checked ? 'Ouverte aux non-membres' : 'Ouverte uniquement aux membres';
        setText(accessPreviewNodes, accessLabel);

        const guestAvailabilityLocked = !openToGuestsInput?.checked;
        if (guestAvailabilityField) {
            guestAvailabilityField.classList.toggle('is-disabled', guestAvailabilityLocked);
        }
        if (guestAvailabilityInput) {
            guestAvailabilityInput.disabled = guestAvailabilityLocked;
            guestAvailabilityInput.setAttribute('aria-disabled', guestAvailabilityLocked ? 'true' : 'false');
        }

        updateImagePreview();
        updateImageSelection();
    }

    function markDirty() {
        dirty = true;
    }

    function clearDirty() {
        dirty = false;
    }

    function setImageValue(value) {
        const targetValue = String(value);
        Array.from(imageSelect.options).forEach((option) => {
            option.selected = option.value === targetValue;
        });
        imageSelect.value = targetValue;
        if (imageIdField) {
            imageIdField.value = targetValue;
        }
        try {
            imageSelect.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (error) {
            // Some browsers can refuse synthetic events on hidden controls.
        }
    }

    function createMediaCard(media) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'admin-media-card';
        button.dataset.mediaCard = '1';
        button.dataset.mediaValue = String(media.id);
        button.dataset.mediaLabel = media.alt || media.path || `Media #${media.id}`;
        button.dataset.mediaPath = media.path || '';
        button.dataset.mediaAlt = media.alt || '';
        button.dataset.mediaKind = media.kind || 'image';
        button.setAttribute('aria-pressed', 'false');

        const thumb = document.createElement('span');
        thumb.className = 'admin-media-card__thumb';

        if (media.path) {
            const assetUrl = new URL(`/uploads/media/${media.path}`, window.location.origin).toString();
            if (button.dataset.mediaKind === 'video') {
                const video = document.createElement('video');
                video.muted = true;
                video.playsInline = true;
                video.preload = 'metadata';
                const source = document.createElement('source');
                source.src = assetUrl;
                video.appendChild(source);
                thumb.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = assetUrl;
                img.alt = media.alt || media.path || 'Media';
                img.loading = 'lazy';
                thumb.appendChild(img);
            }
        } else {
            thumb.classList.add('admin-media-card__thumb--empty');
            thumb.textContent = 'Apercu indisponible';
        }

        const label = document.createElement('strong');
        label.className = 'admin-media-card__label';
        label.textContent = button.dataset.mediaLabel;

        const meta = document.createElement('small');
        meta.className = 'admin-media-card__meta';
        meta.textContent = media.path || 'Aucun fichier';

        button.append(thumb, label, meta);

        return button;
    }

    function ensureImportedMedia(media) {
        if (!mediaPicker || !mediaSelect || !mediaLibrary) {
            return;
        }

        if (!Array.from(mediaSelect.options).some((option) => option.value === String(media.id))) {
            const option = new Option(media.alt || media.path || `Media #${media.id}`, String(media.id), false, false);
            option.dataset.mediaPath = media.path || '';
            option.dataset.mediaAlt = media.alt || '';
            option.dataset.mediaKind = media.kind || 'image';
            mediaSelect.add(option);
        }

        if (!mediaLibrary.querySelector(`[data-media-card][data-media-value="${media.id}"]`)) {
            const card = createMediaCard(media);
            card.addEventListener('click', () => {
                setImageValue(String(media.id));
                markDirty();
            });
            mediaLibrary.appendChild(card);
        }

        refreshMediaCards();
    }

    function openImportPicker() {
        if (!importInput) {
            return;
        }

        if (typeof importInput.showPicker === 'function') {
            try {
                importInput.showPicker();
                return;
            } catch (error) {
                // Some browsers expose showPicker() but refuse it for hidden file inputs.
            }
        }

        importInput.click();
    }

    async function uploadMedia(file) {
        const formData = new FormData();
        formData.append('file', file);

        const response = await fetch('/admin/manage/trix-upload', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Upload failed');
        }

        const payload = await response.json();
        if (!payload || !payload.id || !payload.path) {
            throw new Error('Invalid upload response');
        }

        return payload;
    }

    async function handleImport() {
        const file = importInput?.files?.[0];
        if (!file) {
            return;
        }

        try {
            const media = await uploadMedia(file);
            ensureImportedMedia(media);
            setImageValue(String(media.id));
            updateSummary();
            updateMediaFilter();

            if (importInput) {
                importInput.value = '';
            }
        } catch (error) {
            window.alert("L'import de l'image a echoue.");
            if (importInput) {
                importInput.value = '';
            }
        }
    }

    [titleInput, opponentInput, matchDateInput, matchLocationSelect, categorySelect, priceInput, stockInput, imageSelect, openToGuestsInput, guestAvailabilityInput].forEach((field) => {
        field?.addEventListener('input', () => {
            updateSummary();
            markDirty();
        });

        field?.addEventListener('change', () => {
            updateSummary();
            markDirty();
        });
    });

    mediaSearch?.addEventListener('input', updateMediaFilter);

    mediaLibrary?.addEventListener('click', (event) => {
        const card = event.target instanceof Element ? event.target.closest('[data-media-card]') : null;
        if (!card || !mediaLibrary.contains(card)) {
            return;
        }

        setImageValue(card.dataset.mediaValue || '');
        updateImageSelection();
        updateSummary();
        updateMediaFilter();
        markDirty();
    });

    mediaClearButton?.addEventListener('click', () => {
        setImageValue('');
        updateImageSelection();
        updateSummary();
        updateMediaFilter();
        markDirty();
    });

    importTrigger?.addEventListener('click', (event) => {
        event.preventDefault();
        openImportPicker();
    });

    importInput?.addEventListener('change', () => {
        handleImport();
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

    updateSummary();
    updateMediaFilter();
    updateImageSelection();
    }

    function bootTicketEditor() {
        initTicketEditor();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootTicketEditor, { once: true });
    } else {
        bootTicketEditor();
    }

    document.addEventListener('turbo:load', bootTicketEditor);
})();
