(() => {
    function initPageEditor() {
        const editorRoot = document.querySelector('[data-page-editor="1"]');
        if (!editorRoot || editorRoot.dataset.pageEditorReady === '1') {
            return;
        }

        editorRoot.dataset.pageEditorReady = '1';

    const form = editorRoot.querySelector('[data-page-form="1"]');
    const titleInput = editorRoot.querySelector('[data-page-title="1"]') || editorRoot.querySelector('[name$="[title]"]');
    const slugInput = editorRoot.querySelector('[data-page-slug="1"]') || editorRoot.querySelector('[name$="[slug]"]');
    const contentTextarea = editorRoot.querySelector('[data-page-content="1"]') || editorRoot.querySelector('[name$="[content]"]');
    const previewUrlNodes = editorRoot.querySelectorAll('[data-page-url-preview]');
    const previewUrlFull = editorRoot.querySelector('[data-page-url-full]');
    const slugStateNodes = editorRoot.querySelectorAll('[data-page-slug-state]');
    const titleCounterNodes = editorRoot.querySelectorAll('[data-page-title-counter]');
    const contentStatsNodes = editorRoot.querySelectorAll('[data-page-content-stats]');
    const contentCharsNodes = editorRoot.querySelectorAll('[data-page-content-chars]');
    const readingTimeNode = editorRoot.querySelector('[data-page-content-reading]');
    const titleFeedbackNode = editorRoot.querySelector('[data-page-title-feedback]');
    const regenerateSlugButton = editorRoot.querySelector('[data-page-slug-reset]');
    const previewTriggers = editorRoot.querySelectorAll('[data-page-preview-trigger]');
    const previewForm = editorRoot.querySelector('[data-page-preview-form]');
    const previewTitle = previewForm?.querySelector('[data-preview-title]');
    const previewSlug = previewForm?.querySelector('[data-preview-slug]');
    const previewContent = previewForm?.querySelector('[data-preview-content]');
    const previewImage = previewForm?.querySelector('[data-preview-image]');
    const previewCategory = previewForm?.querySelector('[data-preview-category]');
    const imageIdField = form?.querySelector('[data-page-image-id]');
    const copyUrlButton = editorRoot.querySelector('[data-page-url-copy]');
    const importTrigger = editorRoot.querySelector('[data-page-import-trigger]');
    const importInput = editorRoot.querySelector('[data-page-import-input]');
    const titlePreviewNode = editorRoot.querySelector('[data-page-title-preview]');
    const categorySelect = editorRoot.querySelector('[data-page-category="1"]');
    const categoryLabelNodes = editorRoot.querySelectorAll('[data-page-category-label]');
    const blockInsertButtons = Array.from(editorRoot.querySelectorAll('[data-page-block-insert]'));
    const mediaPicker = editorRoot.querySelector('[data-media-picker]');
    const mediaSelect = mediaPicker?.querySelector('[data-media-select="1"]');
    const mediaSearch = mediaPicker?.querySelector('[data-media-search]');
    const mediaPreview = mediaPicker?.querySelector('[data-media-preview]');
    const mediaClearButton = mediaPicker?.querySelector('[data-media-clear]');
    const mediaLibrary = mediaPicker?.querySelector('[data-media-library]');
    let mediaCards = Array.from(mediaPicker?.querySelectorAll('[data-media-card]') ?? []);
    const mediaLabelNode = editorRoot.querySelector('[data-page-media-label]');
    const mediaPathNode = editorRoot.querySelector('[data-page-media-path]');

    if (!form || !titleInput || !slugInput || !contentTextarea || previewUrlNodes.length === 0) {
        return;
    }

    let dirty = false;
    let slugAutoMode = slugInput.value.trim() === '' || slugify(slugInput.value) === slugify(titleInput.value);
    let syncingSlug = false;
    let editor = null;

    function slugify(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function normalizeSearch(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '');
    }

    function currentSlug() {
        const rawSlug = slugInput.value.trim();
        const defaultSlug = editorRoot.dataset.pageSlugDefault || 'page';

        return rawSlug !== '' ? rawSlug : slugify(titleInput.value) || defaultSlug;
    }

    function setText(nodes, value) {
        nodes.forEach((node) => {
            node.textContent = value;
        });
    }

    function updatePreviewUrl() {
        const slug = currentSlug();
        const template = editorRoot.dataset.pageUrlTemplate || '';
        const url = new URL(template.replace('__slug__', slug), window.location.origin);
        const urlString = url.toString();

        setText(previewUrlNodes, urlString);

        if (previewUrlFull) {
            previewUrlFull.textContent = urlString;
        }
    }

    function updateSlugState() {
        const stateLabel = slugAutoMode ? 'Mode auto' : 'Mode manuel';
        setText(slugStateNodes, stateLabel);
    }

    function updateTitleCounters() {
        const maxLength = titleInput.maxLength > 0 ? titleInput.maxLength : 180;
        const length = titleInput.value.trim().length;
        const counterLabel = `${length}/${maxLength} caracteres`;
        setText(titleCounterNodes, counterLabel);

        if (titlePreviewNode) {
            titlePreviewNode.textContent = titleInput.value.trim() || (editorRoot.dataset.pageTitleDefault || 'Titre de la page');
        }

        if (titleFeedbackNode) {
            titleFeedbackNode.textContent = length === 0 ? 'Titre requis' : 'Titre pret';
        }
    }

    function getSelectedCategoryOption() {
        if (!categorySelect) {
            return null;
        }

        return categorySelect.selectedOptions[0] ?? null;
    }

    function updateCategorySummary() {
        const option = getSelectedCategoryOption();
        const label = option?.textContent?.trim() || 'Aucune categorie';

        setText(categoryLabelNodes, label);
    }

    function getEditorText() {
        if (editor) {
            return editor.getContent({ format: 'text' }).replace(/\u00a0/g, ' ').trim();
        }

        return contentTextarea.value
            .replace(/<[^>]*>/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function updateContentStats() {
        const text = getEditorText();
        const words = text === '' ? 0 : text.split(/\s+/).filter(Boolean).length;
        const chars = text.length;
        const readingMinutes = words === 0 ? 0 : Math.max(1, Math.ceil(words / 200));

        setText(contentStatsNodes, `${words} mot${words > 1 ? 's' : ''}`);
        setText(contentCharsNodes, `${chars} caractere${chars > 1 ? 's' : ''}`);

        if (readingTimeNode) {
            readingTimeNode.textContent = `${readingMinutes} min`;
        }
    }

    function markDirty() {
        dirty = true;
    }

    function clearDirty() {
        dirty = false;
    }

    function syncSlugFromTitle(force = false) {
        if (!force && !slugAutoMode) {
            updatePreviewUrl();
            updateSlugState();
            return;
        }

        const generated = slugify(titleInput.value);
        syncingSlug = true;
        slugInput.value = generated;
        syncingSlug = false;
        slugAutoMode = true;
        updatePreviewUrl();
        updateSlugState();
    }

    function setSlugManualMode() {
        if (!syncingSlug) {
            slugAutoMode = false;
        }

        updatePreviewUrl();
        updateSlugState();
    }

    function getSelectedMediaOption() {
        if (!mediaSelect) {
            return null;
        }

        return mediaSelect.selectedOptions[0] ?? null;
    }

    function updateMediaSummaryFromSelection() {
        const option = getSelectedMediaOption();
        const label = option?.dataset.mediaLabel || option?.textContent?.trim() || 'Aucune image principale';
        const path = option?.dataset.mediaPath || '';

        if (mediaLabelNode) {
            mediaLabelNode.textContent = label;
        }

        if (mediaPathNode) {
            mediaPathNode.textContent = path || 'Aucun fichier associe';
        }
    }

    function updateMediaCardSelection() {
        if (!mediaSelect) {
            return;
        }

        const selectedValue = mediaSelect.value;
        mediaCards.forEach((card) => {
            const isSelected = card.dataset.mediaValue === selectedValue;
            card.classList.toggle('is-selected', isSelected);
            card.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
        });
    }

    function updateMediaPreview() {
        if (!mediaPreview || !mediaSelect) {
            return;
        }

        refreshMediaCards();

        const option = getSelectedMediaOption();
        const path = option?.dataset.mediaPath || '';
        const alt = option?.dataset.mediaAlt || option?.textContent?.trim() || 'Image';
        const kind = option?.dataset.mediaKind || 'image';

        if (!path) {
            mediaPreview.innerHTML = '<div class="admin-media-preview__empty">Aucune image principale selectionnee</div>';
            updateMediaSummaryFromSelection();
            updateMediaCardSelection();
            return;
        }

        const fileUrl = new URL(`/uploads/media/${path}`, window.location.origin).toString();

        if (kind === 'video') {
            mediaPreview.innerHTML = `
                <video class="admin-media-preview__asset" controls preload="metadata">
                    <source src="${fileUrl}">
                </video>
                <div>
                    <strong>${escapeHtml(alt)}</strong>
                    <small>${escapeHtml(path)}</small>
                </div>
            `;
        } else {
            mediaPreview.innerHTML = `
                <img class="admin-media-preview__asset" src="${fileUrl}" alt="${escapeHtml(alt)}" loading="lazy">
                <div>
                    <strong>${escapeHtml(alt)}</strong>
                    <small>${escapeHtml(path)}</small>
                </div>
            `;
        }

        updateMediaSummaryFromSelection();
        updateMediaCardSelection();
    }

    function refreshMediaCards() {
        mediaCards = Array.from(mediaPicker?.querySelectorAll('[data-media-card]') ?? []);
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
                setMediaValue(String(media.id));
                markDirty();
            });
            mediaLibrary.appendChild(card);
        }

        refreshMediaCards();
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function filterMediaCards(query) {
        if (!mediaLibrary) {
            return;
        }

        refreshMediaCards();
        const normalized = normalizeSearch(query);

        mediaCards.forEach((card) => {
            if (card.dataset.mediaValue === '') {
                card.classList.remove('is-hidden');
                return;
            }

            const haystack = normalizeSearch(
                `${card.dataset.mediaLabel || ''} ${card.dataset.mediaPath || ''} ${card.dataset.mediaAlt || ''}`
            );
            const matches = normalized === '' || haystack.includes(normalized);
            card.classList.toggle('is-hidden', !matches);
        });
    }

    function setMediaValue(value) {
        if (!mediaSelect) {
            return;
        }

        const targetValue = String(value);
        Array.from(mediaSelect.options).forEach((option) => {
            option.selected = option.value === targetValue;
        });
        mediaSelect.value = targetValue;
        if (imageIdField) {
            imageIdField.value = targetValue;
        }
        try {
            mediaSelect.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (error) {
            // Keep working even if the browser rejects synthetic events here.
        }
    }

    function collectPreviewData() {
        if (!previewTitle || !previewSlug || !previewContent || !previewImage) {
            return;
        }

        previewTitle.value = titleInput.value.trim();
        previewSlug.value = currentSlug();

        if (editor) {
            previewContent.value = editor.getContent({ format: 'html' });
        } else {
            previewContent.value = contentTextarea.value;
        }

        previewImage.value = mediaSelect?.value || '';
        if (previewCategory) {
            previewCategory.value = categorySelect?.value || '';
        }
    }

    function openPreview() {
        if (!previewForm) {
            return;
        }

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
            ensureImportedMedia(media);
            setMediaValue(media.id);
            updateMediaPreview();

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

    function insertHtmlSnippet(html) {
        if (editor) {
            editor.insertContent(html);
            editor.focus();
            updateContentStats();
            markDirty();
            return;
        }

        const start = contentTextarea.selectionStart ?? contentTextarea.value.length;
        const end = contentTextarea.selectionEnd ?? contentTextarea.value.length;
        const nextValue = `${contentTextarea.value.slice(0, start)}${html}${contentTextarea.value.slice(end)}`;
        contentTextarea.value = nextValue;
        contentTextarea.focus();
        const caret = start + html.length;
        contentTextarea.setSelectionRange(caret, caret);
        updateContentStats();
        markDirty();
    }

    function blockSnippet(kind) {
        switch (kind) {
            case 'title':
                return '<h2>Nouveau bloc</h2><p>Redige ici le contenu du bloc.</p><p><br></p>';
            case 'quote':
                return '<blockquote><p>Une citation ou un message fort.</p></blockquote><p><br></p>';
            case 'separator':
                return '<hr><p><br></p>';
            case 'text':
            default:
                return '<p>Commence ici ton bloc de texte.</p><p><br></p>';
        }
    }

    async function copyCurrentUrl() {
        const text = previewUrlFull?.textContent?.trim() || previewUrlNodes[0]?.textContent?.trim() || '';
        if (!text) {
            return;
        }

        try {
            await navigator.clipboard.writeText(text);
            if (copyUrlButton) {
                const previousLabel = copyUrlButton.textContent;
                copyUrlButton.textContent = 'URL copiee';
                window.setTimeout(() => {
                    copyUrlButton.textContent = previousLabel || "Copier l'URL";
                }, 1400);
            }
        } catch (error) {
            window.prompt("Copie l'URL suivante", text);
        }
    }

    if (window.tinymce && typeof window.tinymce.init === 'function') {
        window.tinymce.init({
            target: contentTextarea,
            height: 680,
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

                h1,
                h2,
                h3,
                h4,
                h5,
                h6 {
                    color: #fff;
                    text-transform: uppercase;
                    letter-spacing: 0.08em;
                    margin: 1.2rem 0 0.6rem;
                }

                h1 { font-size: 2.1rem; }
                h2 { font-size: 1.7rem; }
                h3 { font-size: 1.3rem; }

                p {
                    margin: 0 0 0.9rem;
                }

                a {
                    color: #d40000;
                    text-decoration: underline;
                }

                ul,
                ol {
                    padding-left: 1.35rem;
                    margin: 0 0 1rem;
                }

                li {
                    margin-bottom: 0.35rem;
                }

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
                    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.45);
                    margin: 1rem 0;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 1rem 0;
                }

                th,
                td {
                    border: 1px solid rgba(255, 255, 255, 0.14);
                    padding: 0.55rem 0.7rem;
                    text-align: left;
                }
            `,
            plugins: 'autolink lists link table code fullscreen searchreplace charmap paste wordcount',
            toolbar:
                'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist blockquote hr | link table | removeformat | code fullscreen',
            block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Preformatted=pre',
            valid_elements:
                'a[href|title|target|rel],p[style],br,strong/b,em/i,u,s,ul,ol,li,blockquote[style],hr,' +
                'div[style],span[style],pre[style],code[style],h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],' +
                'table,thead,tbody,tr,th[colspan|rowspan|style],td[colspan|rowspan|style],figure[style],figcaption[style],img[src|alt|title|width|height|loading|style]',
            invalid_elements: 'script,iframe,object,embed,form,input,button,textarea,select',
            extended_valid_elements: 'img[src|alt|title|width|height|loading|style],a[href|title|target|rel]',
            paste_block_drop: false,
            paste_data_images: false,
            paste_remove_styles_if_webkit: true,
            paste_strip_class_attributes: 'all',
            paste_as_text: false,
            setup(instance) {
                editor = instance;
                instance.on('init', () => {
                    clearDirty();
                    updateContentStats();
                });
                instance.on('change keyup undo redo SetContent input', () => {
                    markDirty();
                    updatePreviewUrl();
                    updateContentStats();
                });
            },
        });
    }

    titleInput.addEventListener('input', () => {
        syncSlugFromTitle();
        updateTitleCounters();
        markDirty();
    });

    slugInput.addEventListener('input', () => {
        setSlugManualMode();
        markDirty();
    });

    categorySelect?.addEventListener('change', () => {
        updateCategorySummary();
        markDirty();
    });

    regenerateSlugButton?.addEventListener('click', () => {
        slugAutoMode = true;
        syncSlugFromTitle(true);
        markDirty();
    });

    copyUrlButton?.addEventListener('click', () => {
        copyCurrentUrl();
    });

    blockInsertButtons.forEach((button) => {
        button.addEventListener('click', () => {
            insertHtmlSnippet(blockSnippet(button.dataset.pageBlockInsert || 'text'));
        });
    });

    mediaSearch?.addEventListener('input', (event) => {
        filterMediaCards(event.currentTarget instanceof HTMLInputElement ? event.currentTarget.value : '');
    });

    mediaLibrary?.addEventListener('click', (event) => {
        const card = event.target instanceof Element ? event.target.closest('[data-media-card]') : null;
        if (!card || !mediaLibrary.contains(card)) {
            return;
        }

        setMediaValue(card.dataset.mediaValue || '');
        updateMediaPreview();
        markDirty();
    });

    mediaSelect?.addEventListener('change', () => {
        updateMediaPreview();
        markDirty();
    });

    mediaClearButton?.addEventListener('click', () => {
        setMediaValue('');
        updateMediaPreview();
        markDirty();
    });

    importTrigger?.addEventListener('click', (event) => {
        event.preventDefault();
        importInput?.click();
    });

    importInput?.addEventListener('change', () => {
        handleImport();
    });

    previewTriggers.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            openPreview();
        });
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

    syncSlugFromTitle(false);
    updateTitleCounters();
    updateCategorySummary();
    updatePreviewUrl();
    updateSlugState();
    updateContentStats();
    updateMediaPreview();
    refreshMediaCards();
    filterMediaCards(mediaSearch?.value || '');
    }

    function bootPageEditor() {
        initPageEditor();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootPageEditor, { once: true });
    } else {
        bootPageEditor();
    }

    document.addEventListener('turbo:load', bootPageEditor);
})();