<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Contracts;

interface CacheKeys
{
    /**
     * Generate an HTML cache key.
     *
     * @param array<string, mixed> $data
     */
    public function html(string $template, array $data): string;

    /**
     * Generate an image cache key.
     *
     * @param array<string, mixed> $data
     */
    public function image(string $template, array $data): string;
}
