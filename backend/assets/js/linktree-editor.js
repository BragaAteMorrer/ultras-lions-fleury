(() => {
    const editorRoot = document.querySelector('[data-linktree-editor="1"]');
    if (!editorRoot) {
        return;
    }

    const form = editorRoot.querySelector('[data-linktree-form="1"]');
    const titleInput = form?.querySelector('[name$="[title]"]');
    const descriptionInput = form?.querySelector('[name$="[description]"]');
    const urlInput = form?.querySelector('[name$="[url]"]');
    const iconInput = form?.querySelector('[name$="[icon]"]');
    const positionInput = form?.querySelector('[name$="[position]"]');
    const enabledInput = form?.querySelector('[name$="[enabled]"]');
    const featuredInput = form?.querySelector('[name$="[featured]"]');
    const openInNewTabInput = form?.querySelector('[name$="[openInNewTab]"]');
    const previewForm = editorRoot.querySelector('[data-linktree-preview-form]');
    const previewTriggers = editorRoot.querySelectorAll('[data-page-preview-trigger]');
    const titlePreviewNodes = editorRoot.querySelectorAll('[data-linktree-title-preview]');
    const urlPreviewNodes = editorRoot.querySelectorAll('[data-linktree-url-preview]');
    const statePreviewNodes = editorRoot.querySelectorAll('[data-linktree-state-preview]');
    const iconPreviewNodes = editorRoot.querySelectorAll('[data-linktree-icon-preview]');
    const previewIconNode = editorRoot.querySelector('[data-linktree-preview-icon]');
    const previewTitleNode = editorRoot.querySelector('[data-linktree-preview-title]');
    const previewDescriptionNode = editorRoot.querySelector('[data-linktree-preview-description]');
    const previewUrlNode = editorRoot.querySelector('[data-linktree-preview-url]');
    const previewMetaNode = editorRoot.querySelector('[data-linktree-preview-meta]');
    const previewTitle = previewForm?.querySelector('[data-preview-title]');
    const previewDescription = previewForm?.querySelector('[data-preview-description]');
    const previewUrl = previewForm?.querySelector('[data-preview-url-value]');
    const previewIcon = previewForm?.querySelector('[data-preview-icon]');
    const previewPosition = previewForm?.querySelector('[data-preview-position]');
    const previewEnabled = previewForm?.querySelector('[data-preview-enabled]');
    const previewFeatured = previewForm?.querySelector('[data-preview-featured]');
    const previewOpenInNewTab = previewForm?.querySelector('[data-preview-open-in-new-tab]');

    if (!form || !titleInput || !urlInput || !previewForm) {
        return;
    }

    let dirty = false;

    function setText(nodes, value) {
        nodes.forEach((node) => {
            node.textContent = value;
        });
    }

    function checkedValue(input) {
        return input?.checked ? '1' : '0';
    }

    function getStateLabel() {
        const enabled = enabledInput?.checked ?? true;
        const featured = featuredInput?.checked ?? false;
        const openInNewTab = openInNewTabInput?.checked ?? false;

        return [
            enabled ? 'Actif' : 'Inactif',
            featured ? 'Lien principal' : 'Lien secondaire',
            openInNewTab ? 'Nouvel onglet' : null,
        ]
            .filter(Boolean)
            .join(' · ');
    }

    function getIconClass() {
        const icon = (iconInput?.value.trim() || 'link-45deg').replace(/[^a-z0-9-]/gi, '');
        return `bi bi-${icon}`;
    }

    function updatePreviewCard() {
        const title = titleInput.value.trim() || 'Lien';
        const description = descriptionInput?.value.trim() || 'Sous-titre du lien';
        const url = urlInput.value.trim() || '#';
        const state = getStateLabel();
        const iconName = (iconInput?.value.trim() || 'link-45deg').replace(/[^a-z0-9-]/gi, '');

        setText(titlePreviewNodes, title);
        setText(urlPreviewNodes, url);
        setText(statePreviewNodes, state);
        setText(iconPreviewNodes, iconName);
        setText([previewTitleNode], title);
        setText([previewDescriptionNode], description);
        setText([previewUrlNode], url);
        setText([previewMetaNode], state);

        if (previewIconNode) {
            previewIconNode.className = getIconClass();
        }
    }

    function buildPreviewPayload() {
        if (!previewTitle || !previewDescription || !previewUrl || !previewIcon || !previewPosition || !previewEnabled || !previewFeatured || !previewOpenInNewTab) {
            return;
        }

        previewTitle.value = titleInput.value.trim();
        previewDescription.value = descriptionInput?.value.trim() || '';
        previewUrl.value = urlInput.value.trim();
        previewIcon.value = iconInput?.value.trim() || '';
        previewPosition.value = positionInput?.value || '0';
        previewEnabled.value = checkedValue(enabledInput);
        previewFeatured.value = checkedValue(featuredInput);
        previewOpenInNewTab.value = checkedValue(openInNewTabInput);
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

    [
        titleInput,
        descriptionInput,
        urlInput,
        iconInput,
        positionInput,
        enabledInput,
        featuredInput,
        openInNewTabInput,
    ].forEach((field) => {
        field?.addEventListener('input', () => {
            updatePreviewCard();
            markDirty();
        });

        field?.addEventListener('change', () => {
            updatePreviewCard();
            markDirty();
        });
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

    updatePreviewCard();
})();