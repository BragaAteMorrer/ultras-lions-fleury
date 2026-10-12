<?php

namespace App\Service;

final class PageContentSanitizer
{
    /**
     * @var string[]
     */
    private array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li', 'blockquote', 'hr',
        'a', 'div', 'span', 'pre', 'code',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'figure', 'figcaption', 'img',
    ];

    /**
     * @var array<string, string[]>
     */
    private array $allowedAttributes = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'th' => ['colspan', 'rowspan', 'style'],
        'td' => ['colspan', 'rowspan', 'style'],
        'p' => ['style'],
        'h1' => ['style'],
        'h2' => ['style'],
        'h3' => ['style'],
        'h4' => ['style'],
        'h5' => ['style'],
        'h6' => ['style'],
        'div' => ['style'],
        'span' => ['style'],
        'blockquote' => ['style'],
        'figure' => ['style'],
        'figcaption' => ['style'],
        'pre' => ['style'],
        'code' => ['style'],
    ];

    public function sanitize(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $previous = libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="page-content-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $root = $document->getElementById('page-content-root');
        if (!$root instanceof \DOMElement) {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            return '';
        }

        $this->sanitizeNode($document, $root);

        $sanitized = '';
        foreach ($root->childNodes as $childNode) {
            $sanitized .= $document->saveHTML($childNode) ?: '';
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return trim($sanitized);
    }

    private function sanitizeNode(\DOMDocument $document, \DOMNode $node): void
    {
        if (!$node->hasChildNodes()) {
            return;
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                $this->sanitizeElement($document, $child);
                $this->sanitizeNode($document, $child);
            }
        }
    }

    private function sanitizeElement(\DOMDocument $document, \DOMElement $element): void
    {
        $tagName = strtolower($element->tagName);

        if (in_array($tagName, ['script', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select'], true)) {
            $element->parentNode?->removeChild($element);

            return;
        }

        if (!in_array($tagName, $this->allowedTags, true)) {
            $this->unwrapNode($document, $element);

            return;
        }

        $allowedAttributes = $this->allowedAttributes[$tagName] ?? [];
        foreach (iterator_to_array($element->attributes ?? []) as $attribute) {
            $name = strtolower($attribute->name);
            $value = trim($attribute->value);

            if (str_starts_with($name, 'on')) {
                $element->removeAttributeNode($attribute);
                continue;
            }

            if (!in_array($name, $allowedAttributes, true)) {
                $element->removeAttributeNode($attribute);
                continue;
            }

            if (in_array($name, ['href', 'src'], true) && !$this->isSafeUrl($value)) {
                $element->removeAttributeNode($attribute);
                continue;
            }

            if ($name === 'style') {
                $style = $this->sanitizeStyle($value);
                if ($style === '') {
                    $element->removeAttributeNode($attribute);
                    continue;
                }

                $element->setAttribute('style', $style);
                continue;
            }

            if ($name === 'target' && $value === '_blank') {
                $element->setAttribute('rel', 'noopener noreferrer');
            }
        }

        if ($tagName === 'a') {
            $href = $element->getAttribute('href');
            if ($href !== '' && !$this->isSafeUrl($href)) {
                $element->removeAttribute('href');
            }
        }

        if ($tagName === 'img') {
            $src = $element->getAttribute('src');
            if ($src !== '' && !$this->isSafeUrl($src)) {
                $element->parentNode?->removeChild($element);
            }
        }
    }

    private function unwrapNode(\DOMDocument $document, \DOMElement $element): void
    {
        $parent = $element->parentNode;
        if (!$parent) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    private function sanitizeStyle(string $style): string
    {
        $style = trim($style);
        if ($style === '') {
            return '';
        }

        $allowed = [];
        foreach (explode(';', $style) as $chunk) {
            [$property, $value] = array_map('trim', array_pad(explode(':', $chunk, 2), 2, ''));
            if ($property === '' || $value === '') {
                continue;
            }

            if ($property === 'text-align' && in_array($value, ['left', 'right', 'center', 'justify'], true)) {
                $allowed[] = $property.': '.$value;
            }
        }

        return implode('; ', $allowed);
    }

    private function isSafeUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '#')) {
            return true;
        }

        if (preg_match('/^(?:https?:|mailto:|tel:|\/)/i', $url) === 1) {
            return true;
        }

        return false;
    }
}
