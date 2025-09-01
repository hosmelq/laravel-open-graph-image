<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Rendering\Screenshot;

use HosmelQ\OpenGraphImage\Contracts\Screenshotter;
use HosmelQ\OpenGraphImage\Support\Config;
use Spatie\Browsershot\Browsershot;

class BrowsershotScreenshotter implements Screenshotter
{
    /**
     * {@inheritDoc}
     */
    public function capture(string $html, int $width, int $height): string
    {
        return Browsershot::html($html)
            ->timeout(Config::browsershotTimeout())
            ->waitUntilNetworkIdle()
            ->windowSize($width, $height)
            ->screenshot();
    }
}
