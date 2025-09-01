<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Contracts;

interface Screenshotter
{
    /**
     * Capture a screenshot from HTML.
     */
    public function capture(string $html, int $width, int $height): string;
}
