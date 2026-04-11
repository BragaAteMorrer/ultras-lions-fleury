(() => {
    function limitToolbar(editor) {
        const toolbarId = editor.getAttribute('toolbar');
        const toolbar = toolbarId ? document.getElementById(toolbarId) : editor.toolbarElement;
        if (!toolbar) return;

        const hideSelectors = [
            '.trix-button--icon-code',
        ];

        hideSelectors.forEach((selector) => {
            toolbar.querySelectorAll(selector).forEach((btn) => {
                btn.style.display = 'none';
            });
        });

        toolbar.querySelectorAll('[data-trix-button-group]').forEach((group) => {
            const visibleButtons = Array.from(group.querySelectorAll('button'))
                .filter((btn) => btn.style.display !== 'none');
            if (visibleButtons.length === 0) {
                group.style.display = 'none';
            }
        });
    }

    function setupPreview(editor) {
        const inputId = editor.getAttribute('input');
        if (!inputId) return;

        const input = document.getElementById(inputId);
        if (!input || input.dataset.eaTrixPreview !== '1') return;
        if (editor.dataset.previewReady === '1') return;
        editor.dataset.previewReady = '1';

        const preview = document.createElement('div');
        preview.className = 'ea-trix-preview';

        const title = document.createElement('div');
        title.className = 'ea-trix-preview__title';
        title.textContent = 'Apercu';

        const content = document.createElement('div');
        content.className = 'ea-trix-preview__content';

        preview.appendChild(title);
        preview.appendChild(content);
        editor.insertAdjacentElement('afterend', preview);

        const update = () => {
            const value = input.value || '';
            content.innerHTML = value.trim() !== '' ? value : '<em>Aucun contenu</em>';
        };

        editor.addEventListener('trix-change', update);
        update();
    }

    document.addEventListener('trix-initialize', (event) => {
        const editor = event.target;
        if (!(editor instanceof HTMLElement)) return;
        limitToolbar(editor);
        setupPreview(editor);
    });

    function uploadAttachment(attachment) {
        const file = attachment.file;
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/trix-upload', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', (event) => {
            if (event.lengthComputable) {
                const progress = (event.loaded / event.total) * 100;
                attachment.setUploadProgress(progress);
            }
        });

        xhr.addEventListener('load', () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response && response.url) {
                        attachment.setAttributes({
                            url: response.url,
                            href: response.url,
                        });
                        return;
                    }
                } catch (err) {
                    // fallthrough to remove
                }
            }

            attachment.remove();
        });

        xhr.addEventListener('error', () => {
            attachment.remove();
        });

        xhr.send(formData);
    }

    document.addEventListener('trix-attachment-add', (event) => {
        const attachment = event.attachment;
        if (attachment && attachment.file) {
            uploadAttachment(attachment);
        }
    });
})();
