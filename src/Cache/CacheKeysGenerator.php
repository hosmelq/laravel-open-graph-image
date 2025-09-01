<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Cache;

use function Safe\json_encode;

use HosmelQ\OpenGraphImage\Contracts\CacheKeys;
use HosmelQ\OpenGraphImage\CSS\CSSResolver;
use HosmelQ\OpenGraphImage\Exceptions\TemplateNotFound;
use HosmelQ\OpenGraphImage\Templates\TemplateModificationTimeResolver;
use Safe\Exceptions\JsonException;

class CacheKeysGenerator implements CacheKeys
{
    /**
     * Create a new cache keys generator instance.
     */
    public function __construct(
        protected CSSResolver $cssResolver,
        protected TemplateModificationTimeResolver $templateResolver,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws JsonException
     * @throws TemplateNotFound
     */
    public function html(string $template, array $data): string
    {
        return $this->generateCacheKey('open_graph_html', $template, $data);
    }

    /**
     * {@inheritDoc}
     *
     * @throws TemplateNotFound
     * @throws JsonException
     */
    public function image(string $template, array $data): string
    {
        return $this->generateCacheKey('open_graph_image', $template, $data);
    }

    /**
     * Generate a cache key for the given template and data.
     *
     * @param array<string, mixed> $data
     *
     * @throws JsonException
     * @throws TemplateNotFound
     */
    private function generateCacheKey(string $prefix, string $template, array $data): string
    {
        $payload = [
            'template' => $template,
            'compiled_at' => $this->templateResolver->getModificationTime($template),
            'css_at' => $this->cssResolver->getModificationTime(),
            'data' => $data,
        ];

        ksort($payload['data']);

        return sprintf(
            '%s_%s',
            $prefix,
            hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
        );
    }
}
