(() => {
    const editorRoot = document.querySelector('[data-chant-editor="1"]');
    if (!editorRoot) {
        return;
    }

    const form = editorRoot.querySelector('[data-chant-form="1"]');
    const titleInput = editorRoot.querySelector('[data-chant-title="1"]');
    const lyricsTextarea = editorRoot.querySelector('[data-chant-lyrics="1"]');
    const audioInput = form?.querySelector('[name$="[audioFile]"]');
    const currentAudioPath = editorRoot.dataset.chantCurrentAudioPath || '';
    const titleCounterNodes = editorRoot.querySelectorAll('[data-chant-title-counter]');
    const titleFeedbackNode = editorRoot.querySelector('[data-chant-title-feedback]');
    const lyricsStatsNodes = editorRoot.querySelectorAll('[data-chant-lyrics-stats]');
    const lyricsCharsNodes = editorRoot.querySelectorAll('[data-chant-lyrics-chars]');
    const lyricsReadingNode = editorRoot.querySelector('[data-chant-lyrics-reading]');
    const titlePreviewNodes = editorRoot.querySelectorAll('[data-chant-title-preview]');
    const audioPreviewNodes = editorRoot.querySelectorAll('[data-chant-audio-preview]');
    const audioStatusNodes = editorRoot.querySelectorAll('[data-chant-audio-status]');
    const previewTriggers = editorRoot.querySelectorAll('[data-page-preview-trigger]');
    const previewForm = editorRoot.querySelector('[data-chant-preview-form]');
    const previewTitle = previewForm?.querySelector('[data-preview-title]');
    const previewLyrics = previewForm?.querySelector('[data-preview-lyrics]');
    const previewAudioPath = previewForm?.querySelector('[data-preview-audio-path]');
    const previewAudioLabel = previewForm?.querySelector('[data-preview-audio-label]');

    if (!form || !titleInput || !lyricsTextarea || !previewForm) {
        return;
    }

    let dirty = false;
    let editor = null;

    function setText(nodes, value) {
        nodes.forEach((node) => {
            node.textContent = value;
        });
    }

    function getEditorText() {
        if (editor) {
            return editor.getContent({ format: 'text' }).replace(/\u00a0/g, ' ').trim();
        }

        return lyricsTextarea.value
            .replace(/<[^>]*>/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function updateTitleSummary() {
        const value = titleInput.value.trim() || 'Chant';
        setText(titlePreviewNodes, value);

        const length = titleInput.value.trim().length;
        setText(titleCounterNodes, `${length}/180 caracteres`);

        if (titleFeedbackNode) {
            titleFeedbackNode.textContent = length === 0 ? 'Titre requis' : 'Titre pret';
        }
    }

    function updateLyricsStats() {
        const text = getEditorText();
        const words = text === '' ? 0 : text.split(/\s+/).filter(Boolean).length;
        const chars = text.length;
        const readingMinutes = words === 0 ? 0 : Math.max(1, Math.ceil(words / 180));

        setText(lyricsStatsNodes, `${words} mot${words > 1 ? 's' : ''}`);
        setText(lyricsCharsNodes, `${chars} caractere${chars > 1 ? 's' : ''}`);

        if (lyricsReadingNode) {
            lyricsReadingNode.textContent = `${readingMinutes} min`;
        }
    }

    function updateAudioSummary() {
        const selectedFile = audioInput?.files?.[0] || null;
        const currentAudioName = currentAudioPath ? currentAudioPath.split('/').pop() || currentAudioPath : '';
        const value = selectedFile?.name || currentAudioName || 'Aucun fichier audio';

        setText(audioPreviewNodes, value);
        setText(audioStatusNodes, selectedFile
            ? `Fichier importé: ${selectedFile.name}`
            : currentAudioPath
                ? 'Fichier actuellement enregistré'
                : 'Aucun fichier audio enregistré');
    }

    function markDirty() {
        dirty = true;
    }

    function clearDirty() {
        dirty = false;
    }

    function collectPreviewData() {
        if (!previewTitle || !previewLyrics || !previewAudioPath || !previewAudioLabel) {
            return;
        }

        previewTitle.value = titleInput.value.trim();
        previewLyrics.value = editor ? editor.getContent({ format: 'html' }) : lyricsTextarea.value;
        const selectedFile = audioInput?.files?.[0] || null;
        previewAudioPath.value = selectedFile ? '' : currentAudioPath;
        previewAudioLabel.value = selectedFile?.name || (currentAudioPath ? currentAudioPath.split('/').pop() || currentAudioPath : '');
    }

    function openPreview() {
        collectPreviewData();
        previewForm.requestSubmit();
    }

    if (window.tinymce && typeof window.tinymce.init === 'function') {
        window.tinymce.init({
            target: lyricsTextarea,
            height: 560,
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
            `,
            plugins: 'autolink lists link code fullscreen searchreplace charmap paste wordcount',
            toolbar:
                'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist blockquote hr | link | removeformat | code fullscreen',
            block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Preformatted=pre',
            valid_elements:
                'a[href|title|target|rel],p[style],br,strong/b,em/i,u,s,ul,ol,li,blockquote[style],hr,' +
                'div[style],span[style],pre[style],code[style],h1[style],h2[style],h3[style],h4[style],h5[style],h6[style]',
            invalid_elements: 'script,iframe,object,embed,form,input,button,textarea,select',
            extended_valid_elements: 'a[href|title|target|rel]',
            paste_block_drop: false,
            paste_data_images: false,
            paste_remove_styles_if_webkit: true,
            paste_strip_class_attributes: 'all',
            paste_as_text: false,
            setup(instance) {
                editor = instance;
                instance.on('init', () => {
                    clearDirty();
                    updateLyricsStats();
                });
                instance.on('change keyup undo redo SetContent input', () => {
                    markDirty();
                    updateLyricsStats();
                });
            },
        });
    }

    titleInput.addEventListener('input', () => {
        updateTitleSummary();
        markDirty();
    });

    audioInput?.addEventListener('change', () => {
        updateAudioSummary();
        markDirty();
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

    updateTitleSummary();
    updateAudioSummary();
    updateLyricsStats();
})();

