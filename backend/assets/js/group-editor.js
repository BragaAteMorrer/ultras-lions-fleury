(() => {
    const editorRoot = document.querySelector('[data-group-editor="1"]');
    if (!editorRoot) {
        return;
    }

    const form = editorRoot.querySelector('[data-group-form="1"]');
    const nameInput = editorRoot.querySelector('[data-group-name="1"]');
    const previewTrigger = editorRoot.querySelector('[data-page-preview-trigger]');
    const importInput = editorRoot.querySelector('[data-group-import-input]');
    const importTriggers = Array.from(editorRoot.querySelectorAll('[data-group-import-trigger]'));
    const previewForm = editorRoot.querySelector('[data-group-preview-form]');
    const previewName = previewForm?.querySelector('[data-preview-name]');
    const previewDescription = previewForm?.querySelector('[data-preview-description]');
    const previewHistoireText = previewForm?.querySelector('[data-preview-histoire-text]');
    const previewMentaliteText = previewForm?.querySelector('[data-preview-mentalite-text]');
    const previewFonctionnementText = previewForm?.querySelector('[data-preview-fonctionnement-text]');
    const previewRejoindreText = previewForm?.querySelector('[data-preview-rejoindre-text]');
    const previewSeCarterText = previewForm?.querySelector('[data-preview-se-carter-text]');
    const previewLogo = previewForm?.querySelector('[data-preview-logo]');
    const previewBanner = previewForm?.querySelector('[data-preview-banner]');
    const namePreviewNodes = editorRoot.querySelectorAll('[data-group-name-preview]');
    const nameCounterNode = editorRoot.querySelector('[data-group-name-counter]');
    const nameFeedbackNode = editorRoot.querySelector('[data-group-name-feedback]');
    const contentStatsNodes = editorRoot.querySelectorAll('[data-group-content-stats]');
    const contentCharsNodes = editorRoot.querySelectorAll('[data-group-content-chars]');
    const contentReadingNode = editorRoot.querySelector('[data-group-content-reading]');
    const logoLabelNode = editorRoot.querySelector('[data-group-logo-label]');
    const logoPathNode = editorRoot.querySelector('[data-group-logo-path]');
    const bannerLabelNode = editorRoot.querySelector('[data-group-banner-label]');
    const bannerPathNode = editorRoot.querySelector('[data-group-banner-path]');
    const richTextareas = Array.from(editorRoot.querySelectorAll('textarea[data-group-rich="1"]'));
    const mediaPickers = Array.from(editorRoot.querySelectorAll('[data-media-picker]'));

    if (!form || !nameInput || richTextareas.length === 0) {
        return;
    }

    const editors = new Map();
    let dirty = false;
    let pendingImportRole = 'logo';

    function slugify(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function setText(nodes, value) {
        nodes.forEach((node) => {
            node.textContent = value;
        });
    }

    function markDirty() {
        dirty = true;
    }

    function clearDirty() {
        dirty = false;
    }

    function getFieldName(textarea) {
        return textarea.name.match(/\[(.+)\]$/)?.[1] || textarea.id;
    }

    function getPicker(role) {
        return editorRoot.querySelector(`[data-media-picker][data-media-role="${role}"]`);
    }

    function getSelect(picker) {
        return picker?.querySelector('[data-media-select="1"]');
    }

    function getLibrary(picker) {
        return picker?.querySelector('[data-media-library]');
    }

    function getSearchInput(picker) {
        return picker?.querySelector('[data-media-search]');
    }

    function getPreviewBox(picker) {
        return picker?.querySelector('[data-media-preview]');
    }

    function getSelectedOption(select) {
        return select?.selectedOptions[0] ?? null;
    }

    function getEditorContent(fieldName, format = 'html') {
        const editor = editors.get(fieldName);
        if (editor) {
            return editor.getContent({ format });
        }

        const textarea = richTextareas.find((element) => getFieldName(element) === fieldName);
        return textarea ? textarea.value : '';
    }

    function updateNameSummary() {
        const value = nameInput.value.trim();
        const maxLength = nameInput.maxLength > 0 ? nameInput.maxLength : 120;
        setText(namePreviewNodes, value || 'Page groupe');

        if (nameCounterNode) {
            nameCounterNode.textContent = `${value.length}/${maxLength} caracteres`;
        }

        if (nameFeedbackNode) {
            nameFeedbackNode.textContent = value === '' ? 'Nom requis' : `Slug auto · ${slugify(value)}`;
        }
    }

    function updateContentStats() {
        const text = richTextareas
            .map((textarea) => getEditorContent(getFieldName(textarea), 'text'))
            .join(' ')
            .replace(/\u00a0/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();

        const words = text === '' ? 0 : text.split(/\s+/).filter(Boolean).length;
        const chars = text.length;
        const readingMinutes = words === 0 ? 0 : Math.max(1, Math.ceil(words / 200));

        setText(contentStatsNodes, `${words} mot${words > 1 ? 's' : ''}`);
        setText(contentCharsNodes, `${chars} caractere${chars > 1 ? 's' : ''}`);

        if (contentReadingNode) {
            contentReadingNode.textContent = `${readingMinutes} min`;
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function updateMediaSummary(picker) {
        const role = picker.dataset.mediaRole || 'media';
        const select = getSelect(picker);
        const option = getSelectedOption(select);
        const label = option?.dataset.mediaLabel || option?.textContent?.trim() || `Aucun ${role}`;
        const path = option?.dataset.mediaPath || '';

        if (role === 'logo') {
            if (logoLabelNode) {
                logoLabelNode.textContent = label;
            }
            if (logoPathNode) {
                logoPathNode.textContent = path || 'Aucun fichier associe';
            }
        }

        if (role === 'banner') {
            if (bannerLabelNode) {
                bannerLabelNode.textContent = label;
            }
            if (bannerPathNode) {
                bannerPathNode.textContent = path || 'Aucun fichier associe';
            }
        }
    }

    function updateMediaPreview(picker) {
        const select = getSelect(picker);
        const preview = getPreviewBox(picker);
        const cards = Array.from(picker.querySelectorAll('[data-media-card]'));

        if (!select || !preview) {
            return;
        }

        const option = getSelectedOption(select);
        const path = option?.dataset.mediaPath || '';
        const alt = option?.dataset.mediaAlt || option?.textContent?.trim() || 'Image';
        const kind = option?.dataset.mediaKind || 'image';
        const selectedValue = select.value;

        cards.forEach((card) => {
            const isSelected = card.dataset.mediaValue === selectedValue;
            card.classList.toggle('is-selected', isSelected);
            card.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
        });

        if (!path) {
            preview.innerHTML = '<div class="admin-media-preview__empty">Aucun visuel selectionne</div>';
            updateMediaSummary(picker);
            return;
        }

        const fileUrl = new URL(`/uploads/media/${path}`, window.location.origin).toString();
        if (kind === 'video') {
            preview.innerHTML = `
                <video class="admin-media-preview__asset" controls preload="metadata">
                    <source src="${fileUrl}">
                </video>
                <div>
                    <strong>${escapeHtml(alt)}</strong>
                    <small>${escapeHtml(path)}</small>
                </div>
            `;
        } else {
            preview.innerHTML = `
                <img class="admin-media-preview__asset" src="${fileUrl}" alt="${escapeHtml(alt)}" loading="lazy">
                <div>
                    <strong>${escapeHtml(alt)}</strong>
                    <small>${escapeHtml(path)}</small>
                </div>
            `;
        }

        updateMediaSummary(picker);
    }

    function filterMediaCards(picker, query) {
        const normalized = slugify(query).replace(/-/g, '');
        const cards = Array.from(picker.querySelectorAll('[data-media-card]'));

        cards.forEach((card) => {
            if (card.dataset.mediaValue === '') {
                card.classList.remove('is-hidden');
                return;
            }

            const haystack = `${card.dataset.mediaLabel || ''} ${card.dataset.mediaPath || ''} ${card.dataset.mediaAlt || ''}`
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '');
            card.classList.toggle('is-hidden', !(normalized === '' || haystack.includes(normalized)));
        });
    }

    function setMediaValue(picker, value) {
        const select = getSelect(picker);
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
        const select = getSelect(picker);
        const library = getLibrary(picker);
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
            library.appendChild(createMediaCard(media));
        }
    }

    function setPreviewField(node, value) {
        if (node) {
            node.value = value;
        }
    }

    function buildPreviewPayload() {
        setPreviewField(previewName, nameInput.value.trim());
        setPreviewField(previewDescription, getEditorContent('description'));
        setPreviewField(previewHistoireText, getEditorContent('histoireText'));
        setPreviewField(previewMentaliteText, getEditorContent('mentaliteText'));
        setPreviewField(previewFonctionnementText, getEditorContent('fonctionnementText'));
        setPreviewField(previewRejoindreText, getEditorContent('rejoindreText'));
        setPreviewField(previewSeCarterText, getEditorContent('seCarterText'));
        setPreviewField(previewLogo, getSelect(getPicker('logo'))?.value || '');
        setPreviewField(previewBanner, getSelect(getPicker('banner'))?.value || '');
    }

    function openPreview() {
        if (!previewForm) {
            return;
        }

        buildPreviewPayload();
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
            const targetPicker = getPicker(pendingImportRole) || getPicker('logo');

            mediaPickers.forEach((picker) => {
                ensureImportedMedia(picker, media);
                updateMediaPreview(picker);
            });

            if (targetPicker) {
                setMediaValue(targetPicker, media.id);
                updateMediaPreview(targetPicker);
            }

            if (importInput) {
                importInput.value = '';
            }

            markDirty();
        } catch (error) {
            window.alert("L'import de l'image a echoue.");
            if (importInput) {
                importInput.value = '';
            }
        }
    }

    if (window.tinymce && typeof window.tinymce.init === 'function') {
        richTextareas.forEach((textarea) => {
            const fieldName = getFieldName(textarea);

            window.tinymce.init({
                target: textarea,
                height: 360,
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
                    h1, h2, h3, h4, h5, h6 {
                        color: #fff;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
                        margin: 1.1rem 0 0.55rem;
                    }
                    a { color: #d40000; text-decoration: underline; }
                    ul, ol { padding-left: 1.35rem; margin: 0 0 1rem; }
                    li { margin-bottom: 0.35rem; }
                    blockquote {
                        border-left: 0.25rem solid #d40000;
                        background: rgba(10, 10, 10, 0.6);
                        padding: 0.75rem 1rem;
                        margin: 1rem 0;
                    }
                `,
                plugins: 'lists link code fullscreen searchreplace paste wordcount',
                toolbar: 'undo redo | blocks | bold italic underline | bullist numlist blockquote | link | removeformat | code fullscreen',
                block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Preformatted=pre',
                valid_elements:
                    'a[href|title|target|rel],p[style],br,strong/b,em/i,u,s,ul,ol,li,blockquote[style],hr,' +
                    'div[style],span[style],pre[style],code[style],h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],' +
                    'img[src|alt|title|width|height|loading|style]',
                invalid_elements: 'script,iframe,object,embed,form,input,button,textarea,select',
                extended_valid_elements: 'img[src|alt|title|width|height|loading|style],a[href|title|target|rel]',
                paste_block_drop: false,
                paste_data_images: false,
                paste_remove_styles_if_webkit: true,
                paste_strip_class_attributes: 'all',
                paste_as_text: false,
                setup(instance) {
                    editors.set(fieldName, instance);
                    instance.on('init', () => {
                        updateContentStats();
                        clearDirty();
                    });
                    instance.on('change keyup undo redo SetContent input', () => {
                        markDirty();
                        updateContentStats();
                    });
                },
            });
        });
    }

    nameInput.addEventListener('input', () => {
        updateNameSummary();
        markDirty();
    });

    mediaPickers.forEach((picker) => {
        const search = getSearchInput(picker);
        const select = getSelect(picker);
        const clear = picker.querySelector('[data-media-clear]');
        const cards = Array.from(picker.querySelectorAll('[data-media-card]'));

        search?.addEventListener('input', (event) => {
            filterMediaCards(picker, event.currentTarget instanceof HTMLInputElement ? event.currentTarget.value : '');
        });

        picker.addEventListener('click', (event) => {
            const card = event.target instanceof Element ? event.target.closest('[data-media-card]') : null;
            if (!card || !picker.contains(card)) {
                return;
            }

            setMediaValue(picker, card.dataset.mediaValue || '');
            updateMediaPreview(picker);
            markDirty();
        });

        select?.addEventListener('change', () => {
            updateMediaPreview(picker);
            markDirty();
        });

        clear?.addEventListener('click', () => {
            setMediaValue(picker, '');
            updateMediaPreview(picker);
            markDirty();
        });

        updateMediaPreview(picker);
        filterMediaCards(picker, search?.value || '');
    });

    importTriggers.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            pendingImportRole = button.dataset.groupImportTrigger || 'logo';
            importInput?.click();
        });
    });

    importInput?.addEventListener('change', () => {
        handleImport();
    });

    previewTrigger?.addEventListener('click', (event) => {
        event.preventDefault();
        openPreview();
    });

    form.addEventListener('submit', () => {
        editors.forEach((instance) => instance.save());
        clearDirty();
    });

    window.addEventListener('beforeunload', (event) => {
        if (!dirty) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    });

    updateNameSummary();
    updateContentStats();
})();
