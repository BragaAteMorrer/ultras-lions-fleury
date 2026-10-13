<?php

namespace App\Twig;

use App\Service\PageContentSanitizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HtmlSanitizerExtension extends AbstractExtension
{
    public function __construct(
        private readonly PageContentSanitizer $pageContentSanitizer,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sanitize_page_html', [$this, 'sanitizePageHtml']),
        ];
    }

    public function sanitizePageHtml(?string $html): string
    {
        return $this->pageContentSanitizer->sanitize($html);
    }
}
