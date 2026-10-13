(() => {
    const editorRoot = document.querySelector('[data-media-editor="1"]');
    if (!editorRoot) {
        return;
    }

    if (window.__mediaEditorInitialized) {
        return;
    }

    window.__mediaEditorInitialized = true;

    const form = editorRoot.querySelector('[data-media-form="1"]');
    const pathInput = form?.querySelector('[name$="[path]"]');
    const altInput = form?.querySelector('[name$="[alt]"]');
    const galleryInput = form?.querySelector('[name$="[gallery]"]');
    const eventInput = form?.querySelector('[name$="[event]"]');
    const postInput = form?.querySelector('[name$="[post]"]');
    const merchInput = form?.querySelector('[name$="[merch]"]');
    const groupPageInput = form?.querySelector('[name$="[groupPage]"]');
    const previewForm = editorRoot.querySelector('[data-media-preview-form]');
    const previewTriggers = editorRoot.querySelectorAll('[data-page-preview-trigger]');
    const previewVisualNode = editorRoot.querySelector('[data-media-preview-visual]');
    const previewTitleNode = editorRoot.querySelector('[data-media-preview-title]');
    const previewPathNode = editorRoot.querySelector('[data-media-preview-path]');
    const pathPreviewNode = editorRoot.querySelector('[data-media-path-preview]');
    const altPreviewNode = editorRoot.querySelector('[data-media-alt-preview]');
    const kindPreviewNode = editorRoot.querySelector('[data-media-kind-preview]');
    const previewPath = previewForm?.querySelector('[data-preview-path]');
    const previewAlt = previewForm?.querySelector('[data-preview-alt]');
    const previewGallery = previewForm?.querySelector('[data-preview-gallery]');
    const previewEvent = previewForm?.querySelector('[data-preview-event]');
    const previewPost = previewForm?.querySelector('[data-preview-post]');
    const previewMerch = previewForm?.querySelector('[data-preview-merch]');
    const previewGroupPage = previewForm?.querySelector('[data-preview-group-page]');
    const importTrigger = editorRoot.querySelector('[data-media-import-trigger]');
    const importInput = editorRoot.querySelector('[data-media-import-input]');

    if (!form || !pathInput || !altInput || !previewForm) {
        return;
    }

    let dirty = false;
    let previewObjectUrl = null;

    function setText(node, value) {
        if (node) {
            node.textContent = value;
        }
    }

    function getSelectedFile() {
        return importInput?.files?.[0] || null;
    }

    function getPreviewPath() {
        const file = getSelectedFile();
        if (file) {
            return file.name;
        }

        return pathInput.value.trim();
    }

    function getPreviewAlt() {
        const file = getSelectedFile();
        if (file && altInput.value.trim() === '') {
            return file.name;
        }

        return altInput.value.trim() || 'Media sans titre';
    }

    function getPreviewKind() {
        const file = getSelectedFile();
        if (file && file.type.startsWith('video/')) {
            return 'video';
        }

        return resolveKind(getPreviewPath());
    }

    function resolveKind(path) {
        const lower = String(path || '').toLowerCase();
        return /\.(mp4|webm|mov|m4v|avi)$/i.test(lower) ? 'video' : 'image';
    }

    function buildFileUrl(path) {
        return new URL(`/uploads/media/${path}`, window.location.origin).toString();
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function updatePreviewCard() {
        const file = getSelectedFile();
        const path = getPreviewPath();
        const alt = getPreviewAlt();
        const kind = getPreviewKind();

        setText(pathPreviewNode, path || 'Aucun chemin defini');
        setText(altPreviewNode, alt);
        setText(kindPreviewNode, kind === 'video' ? 'Video' : 'Image');
        setText(previewTitleNode, alt);
        setText(previewPathNode, path || 'Aucun chemin defini');

        if (!previewVisualNode) {
            return;
        }

        if (!path) {
            previewVisualNode.innerHTML = '<div class="media-preview-card__empty">Aucun fichier renseigne</div>';
            return;
        }

        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = null;
        }

        const fileUrl = file ? (previewObjectUrl = URL.createObjectURL(file)) : buildFileUrl(path);
        if (kind === 'video') {
            previewVisualNode.innerHTML = `
                <video controls preload="metadata">
                    <source src="${escapeHtml(fileUrl)}">
                </video>
            `;
        } else {
            previewVisualNode.innerHTML = `<img src="${escapeHtml(fileUrl)}" alt="${escapeHtml(alt)}">`;
        }
    }

    function buildPreviewPayload() {
        if (!previewPath || !previewAlt || !previewGallery || !previewEvent || !previewPost || !previewMerch || !previewGroupPage) {
            return;
        }

        previewPath.value = pathInput.value.trim();
        previewAlt.value = altInput.value.trim();
        previewGallery.value = galleryInput?.value || '';
        previewEvent.value = eventInput?.value || '';
        previewPost.value = postInput?.value || '';
        previewMerch.value = merchInput?.value || '';
        previewGroupPage.value = groupPageInput?.value || '';
    }

    function openPreview() {
        buildPreviewPayload();
        previewForm.requestSubmit();
    }

    function markDirty() {
        dirty = true;
    }

    function clearDirty() {
        dirty = false;
    }

    [pathInput, altInput, galleryInput, eventInput, postInput, merchInput, groupPageInput].forEach((field) => {
        field?.addEventListener('input', () => {
            updatePreviewCard();
            markDirty();
        });

        field?.addEventListener('change', () => {
            updatePreviewCard();
            markDirty();
        });
    });

    importTrigger?.addEventListener('click', (event) => {
        event.preventDefault();
        importInput?.click();
    });

    importInput?.addEventListener('change', () => {
        updatePreviewCard();
        markDirty();
    });

    importInput?.addEventListener('input', () => {
        updatePreviewCard();
        markDirty();
    });

    previewTriggers.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            openPreview();
        });
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

    window.addEventListener('pagehide', () => {
        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = null;
        }
    });

    updatePreviewCard();
})();
