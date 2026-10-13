(() => {
    const editorRoot = document.querySelector('[data-event-editor="1"]');
    if (!editorRoot) {
        return;
    }

    const form = editorRoot.querySelector('[data-event-form="1"]');
    const titleInput = editorRoot.querySelector('[data-event-title="1"]');
    const descriptionTextarea = editorRoot.querySelector('[data-event-description="1"]');
    const dateInput = form?.querySelector('[name$="[date]"]');
    const categorySelect = form?.querySelector('[name$="[category]"]');
    const opponentInput = form?.querySelector('[name$="[opponent]"]');
    const matchLocationSelect = form?.querySelector('[name$="[matchLocation]"]');
    const seasonInput = form?.querySelector('[name$="[season]"]');
    const journeeInput = form?.querySelector('[name$="[journee]"]');
    const previewForm = editorRoot.querySelector('[data-event-preview-form]');
    const previewTitle = previewForm?.querySelector('[data-preview-title]');
    const previewDate = previewForm?.querySelector('[data-preview-date]');
    const previewCategory = previewForm?.querySelector('[data-preview-category]');
    const previewOpponent = previewForm?.querySelector('[data-preview-opponent]');
    const previewMatchLocation = previewForm?.querySelector('[data-preview-match-location]');
    const previewSeason = previewForm?.querySelector('[data-preview-season]');
    const previewJournee = previewForm?.querySelector('[data-preview-journee]');
    const previewDescription = previewForm?.querySelector('[data-preview-description]');
    const previewImage = previewForm?.querySelector('[data-preview-image]');
    const previewMediaIds = previewForm?.querySelector('[data-preview-media-ids]');
    const previewTriggers = editorRoot.querySelectorAll('[data-page-preview-trigger]');
    const importTrigger = editorRoot.querySelector('[data-event-import-trigger]');
    const galleryImportTrigger = editorRoot.querySelector('[data-event-gallery-import-trigger]');
    const importInput = editorRoot.querySelector('[data-event-import-input]');
    const titlePreviewNode = editorRoot.querySelector('[data-event-title-preview]');
    const titleFeedbackNode = editorRoot.querySelector('[data-event-title-feedback]');
    const titleCounterNodes = editorRoot.querySelectorAll('[data-event-title-counter]');
    const datePreviewNode = editorRoot.querySelector('[data-event-date-preview]');
    const imageLabelNode = editorRoot.querySelector('[data-event-image-label]');
    const imagePathNode = editorRoot.querySelector('[data-event-image-path]');
    const mediaCountNode = editorRoot.querySelector('[data-event-media-count]');
    const mediaSummaryNode = editorRoot.querySelector('[data-event-media-summary]');
    const descriptionStatsNode = editorRoot.querySelector('[data-event-description-stats]');
    const pickers = Array.from(editorRoot.querySelectorAll('[data-media-picker]'));
    let pendingImportRole = 'image';

    if (!form || !titleInput || !descriptionTextarea || !previewForm) {
        return;
    }

    let dirty = false;
    let editor = null;

    function sluglessDateValue() {
        return dateInput?.value || '';
    }

    function setText(nodes, value) {
        nodes.forEach((node) => {
            node.textContent = value;
        });
    }

    function normalizeSearch(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '');
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getEditorText() {
        if (editor) {
            return editor.getContent({ format: 'text' }).replace(/\u00a0/g, ' ').trim();
        }

        return descriptionTextarea.value
            .replace(/<[^>]*>/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function updateTitleState() {
        const title = titleInput.value.trim() || 'Titre de l evenement';
        const length = titleInput.value.trim().length;
        setText(titleCounterNodes, `${length}/180 caracteres`);
        if (titlePreviewNode) {
            titlePreviewNode.textContent = title;
        }
        if (titleFeedbackNode) {
            titleFeedbackNode.textContent = length === 0 ? 'Titre requis' : 'Titre pret';
        }
    }

    function updateDescriptionStats() {
        const text = getEditorText();
        const words = text === '' ? 0 : text.split(/\s+/).filter(Boolean).length;
        const chars = text.length;
        if (descriptionStatsNode) {
            descriptionStatsNode.textContent = `${words} mot${words > 1 ? 's' : ''}`;
        }
        const previewCountNode = editorRoot.querySelector('[data-event-description-stats]');
        if (previewCountNode) {
            previewCountNode.textContent = `${words} mot${words > 1 ? 's' : ''}`;
        }
        const minutes = words === 0 ? 0 : Math.max(1, Math.ceil(words / 180));
        const readingNode = editorRoot.querySelector('[data-event-description-reading]');
        if (readingNode) {
            readingNode.textContent = `${minutes} min`;
        }
        const charsNode = editorRoot.querySelector('[data-event-description-chars]');
        if (charsNode) {
            charsNode.textContent = `${chars} caractere${chars > 1 ? 's' : ''}`;
        }
    }

    function updateSummary() {
        if (datePreviewNode) {
            datePreviewNode.textContent = dateInput?.value || 'Aucune date';
        }

        const imagePicker = pickers.find((picker) => picker.dataset.eventMediaRole === 'image');
        const galleryPicker = pickers.find((picker) => picker.dataset.eventMediaRole === 'gallery');

        const imageSelect = imagePicker?.querySelector('[data-media-select="1"]');
        const imageOption = imageSelect?.selectedOptions[0] ?? null;
        const imageLabel = imageOption?.dataset.mediaLabel || imageOption?.textContent?.trim() || 'Aucune image principale';
        const imagePath = imageOption?.dataset.mediaPath || '';
        if (imageLabelNode) {
            imageLabelNode.textContent = imageLabel;
        }
        if (imagePathNode) {
            imagePathNode.textContent = imagePath || 'Aucun fichier associe';
        }

        const gallerySelect = galleryPicker?.querySelector('[data-event-media-select="1"]');
        const selectedOptions = Array.from(gallerySelect?.selectedOptions ?? []);
        if (mediaCountNode) {
            mediaCountNode.textContent = String(selectedOptions.length);
        }
        if (mediaSummaryNode) {
            mediaSummaryNode.textContent = `${selectedOptions.length} media${selectedOptions.length > 1 ? 's' : ''}`;
        }
    }

    function markDirty() {
        dirty = true;
    }

    function clearDirty() {
        dirty = false;
    }

    function getSelectValue(select) {
        return select?.value || '';
    }

    function getMediaSelect(picker) {
        return picker?.querySelector('[data-event-media-select="1"]');
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

    function ensureImportedMedia(picker, media) {
        const select = getMediaSelect(picker);
        const library = picker?.querySelector('[data-media-library]');

        if (!select || !library) {
            return;
        }

        if (!Array.from(select.options).some((option) => option.value === String(media.id))) {
            const option = new Option(media.alt || media.path || `Media #${media.id}`, String(media.id), false, false);
            option.dataset.mediaPath = media.path || '';
            option.dataset.mediaAlt = media.alt || '';
            option.dataset.mediaKind = media.kind || 'image';
            select.add(option);
        }

        if (!library.querySelector(`[data-media-card][data-media-value="${media.id}"]`)) {
            const card = createMediaCard(media);
            card.addEventListener('click', () => {
                const select = getMediaSelect(picker);
                if (!select) {
                    return;
                }

                if (select.multiple) {
                    toggleMultipleSelect(select, String(media.id));
                } else {
                    setSingleSelect(select, String(media.id));
                }

                updatePicker(picker);
                updateSummary();
                markDirty();
            });
            library.appendChild(card);
        }
    }

    function updatePicker(picker) {
        const search = picker.querySelector('[data-media-search]');
        const select = getMediaSelect(picker);
        const cards = Array.from(picker.querySelectorAll('[data-media-card]'));
        const isMultiple = select?.multiple ?? false;
        const normalizedQuery = normalizeSearch(search?.value || '');

        cards.forEach((card) => {
            if (card.dataset.mediaValue === '') {
                card.classList.remove('is-hidden');
                return;
            }

            const haystack = normalizeSearch(
                `${card.dataset.mediaLabel || ''} ${card.dataset.mediaPath || ''} ${card.dataset.mediaAlt || ''}`
            );
            const matches = normalizedQuery === '' || haystack.includes(normalizedQuery);
            card.classList.toggle('is-hidden', !matches);

            const isSelected = isMultiple
                ? Array.from(select?.selectedOptions ?? []).some((option) => option.value === card.dataset.mediaValue)
                : select?.value === card.dataset.mediaValue;
            card.classList.toggle('is-selected', isSelected);
            card.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
        });
    }

    function setSingleSelect(select, value) {
        if (!select) {
            return;
        }

        const targetValue = String(value);
        Array.from(select.options).forEach((option) => {
            option.selected = option.value === targetValue;
        });
        select.value = targetValue;
        try {
            select.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (error) {
            // Some browsers reject synthetic change events on hidden selects.
        }
    }

    function toggleMultipleSelect(select, value) {
        if (!select) {
            return;
        }

        const values = Array.from(select.selectedOptions).map((option) => option.value);
        const index = values.indexOf(value);
        if (index === -1) {
            values.push(value);
        } else {
            values.splice(index, 1);
        }

        Array.from(select.options).forEach((option) => {
            option.selected = values.includes(option.value);
        });

        try {
            select.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (error) {
            // Keep the picker usable if the synthetic event is blocked.
        }
    }

    function addMultipleSelectValue(select, value) {
        if (!select) {
            return;
        }

        const targetValue = String(value);
        const values = Array.from(select.selectedOptions).map((option) => option.value);

        if (!values.includes(targetValue)) {
            values.push(targetValue);
        }

        Array.from(select.options).forEach((option) => {
            option.selected = values.includes(option.value);
        });

        try {
            select.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (error) {
            // Keep the picker usable if the synthetic event is blocked.
        }
    }

    function collectPreviewData() {
        if (!previewTitle || !previewDate || !previewCategory || !previewOpponent || !previewMatchLocation || !previewSeason || !previewJournee || !previewDescription || !previewImage || !previewMediaIds) {
            return;
        }

        const imagePicker = pickers.find((picker) => picker.dataset.eventMediaRole === 'image');
        const galleryPicker = pickers.find((picker) => picker.dataset.eventMediaRole === 'gallery');
        const imageSelect = getMediaSelect(imagePicker);
        const gallerySelect = getMediaSelect(galleryPicker);
        const imageValue = imageSelect?.value || '';

        previewTitle.value = titleInput.value.trim();
        previewDate.value = sluglessDateValue();
        previewCategory.value = getSelectValue(categorySelect);
        previewOpponent.value = opponentInput?.value.trim() || '';
        previewMatchLocation.value = getSelectValue(matchLocationSelect);
        previewSeason.value = seasonInput?.value.trim() || '';
        previewJournee.value = journeeInput?.value.trim() || '';
        previewDescription.value = editor ? editor.getContent({ format: 'html' }) : descriptionTextarea.value;
        previewImage.value = imageValue;
        previewMediaIds.value = Array.from(gallerySelect?.selectedOptions ?? []).map((option) => option.value).join(',');
    }

    function openPreview() {
        collectPreviewData();
        previewForm.requestSubmit();
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
            const targetPicker = pickers.find((picker) => picker.dataset.eventMediaRole === pendingImportRole)
                || pickers.find((picker) => picker.dataset.eventMediaRole === 'image');

            pickers.forEach((picker) => {
                ensureImportedMedia(picker, media);
                updatePicker(picker);
            });

            if (targetPicker) {
                const targetSelect = getMediaSelect(targetPicker);
                if (targetSelect?.multiple) {
                    addMultipleSelectValue(targetSelect, String(media.id));
                } else {
                    setSingleSelect(targetSelect, String(media.id));
                }
                updatePicker(targetPicker);
                updateSummary();
            }

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

    if (window.tinymce && typeof window.tinymce.init === 'function') {
        window.tinymce.init({
            target: descriptionTextarea,
            height: 520,
            menubar: false,
            branding: false,
            promotion: false,
            resize: true,
            toolbar_sticky: true,
            toolbar_sticky_offset: 16,
            statusbar: true,
            skin: 'oxide-dark',
            content_css: false,
            body_class: 'rich-content',
            content_style: `
                body {
                    margin: 1rem;
                    color: #ddd;
                    background: transparent;
                    font-family: "Oswald", system-ui, sans-serif;
                    font-size: 1rem;
                    line-height: 1.7;
                }

                p { margin: 0 0 0.9rem; }
                a { color: #d40000; text-decoration: underline; }
                ul, ol { padding-left: 1.35rem; margin: 0 0 1rem; }
                li { margin-bottom: 0.35rem; }
                blockquote {
                    border-left: 0.25rem solid #d40000;
                    background: rgba(10, 10, 10, 0.6);
                    padding: 0.75rem 1rem;
                    margin: 1rem 0;
                }
                img {
                    max-width: 100%;
                    height: auto;
                    display: block;
                    border-radius: 0.5rem;
                }
            `,
            plugins: 'autolink lists link table code fullscreen searchreplace charmap paste wordcount',
            toolbar:
                'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist blockquote hr | link table | removeformat | code fullscreen',
            block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Preformatted=pre',
            valid_elements:
                'a[href|title|target|rel],p[style],br,strong/b,em/i,u,s,ul,ol,li,blockquote[style],hr,' +
                'div[style],span[style],pre[style],code[style],h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],img[src|alt|title|width|height|loading|style]',
            invalid_elements: 'script,iframe,object,embed,form,input,button,textarea,select',
            paste_block_drop: false,
            paste_data_images: false,
            paste_remove_styles_if_webkit: true,
            paste_strip_class_attributes: 'all',
            paste_as_text: false,
            setup(instance) {
                editor = instance;
                instance.on('init', () => {
                    clearDirty();
                    updateDescriptionStats();
                });
                instance.on('change keyup undo redo SetContent input', () => {
                    markDirty();
                    updateDescriptionStats();
                });
            },
        });
    }

    titleInput.addEventListener('input', () => {
        updateTitleState();
        markDirty();
    });

    dateInput?.addEventListener('input', () => {
        updateSummary();
        markDirty();
    });

    [categorySelect, opponentInput, matchLocationSelect, seasonInput, journeeInput].forEach((field) => {
        field?.addEventListener('input', () => {
            markDirty();
            updateSummary();
        });
        field?.addEventListener('change', () => {
            markDirty();
            updateSummary();
        });
    });

    pickers.forEach((picker) => {
        const search = picker.querySelector('[data-media-search]');
        const select = getMediaSelect(picker);
        const cards = Array.from(picker.querySelectorAll('[data-media-card]'));

        search?.addEventListener('input', (event) => {
            if (event.currentTarget instanceof HTMLInputElement) {
                updatePicker(picker);
            }
        });

        picker.addEventListener('click', (event) => {
            const card = event.target instanceof Element ? event.target.closest('[data-media-card]') : null;
            if (!card || !picker.contains(card) || !select) {
                return;
            }

            if (select.multiple) {
                toggleMultipleSelect(select, card.dataset.mediaValue || '');
            } else {
                setSingleSelect(select, card.dataset.mediaValue || '');
            }
            markDirty();
            updateSummary();
            updatePicker(picker);
        });

        select?.addEventListener('change', () => {
            updatePicker(picker);
            updateSummary();
            markDirty();
        });

        updatePicker(picker);
    });

    previewTriggers.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            openPreview();
        });
    });

    importTrigger?.addEventListener('click', (event) => {
        event.preventDefault();
        pendingImportRole = 'image';
        importInput?.click();
    });

    galleryImportTrigger?.addEventListener('click', (event) => {
        event.preventDefault();
        pendingImportRole = 'gallery';
        importInput?.click();
    });

    importInput?.addEventListener('change', () => {
        handleImport();
    });

    form.addEventListener('submit', () => {
        if (editor) {
            editor.save();
        }
        clearDirty();
    });

    window.addEventListener('beforeunload', (event) => {
        if (!dirty) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    });

    updateTitleState();
    updateDescriptionStats();
    updateSummary();
})();
