<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Cache;

use HosmelQ\OpenGraphImage\Support\Config;
use Illuminate\Contracts\Cache\Repository;

class RenderingCacheManager
{
    /**
     * Create a new rendering cache manager instance.
     */
    public function __construct(
        protected Repository $htmlCache,
        protected Repository $imageCache,
    ) {
    }

    /**
     * Get cached HTML content.
     */
    public function getHtml(string $key): null|string
    {
        if (! Config::cacheViewsEnabled()) {
            return null;
        }

        /** @var null|string */
        return $this->htmlCache->get($key);
    }

    /**
     * Get cached image content.
     */
    public function getImage(string $key): null|string
    {
        if (! Config::cacheImagesEnabled()) {
            return null;
        }

        /** @var null|string */
        return $this->imageCache->get($key);
    }

    /**
     * Store HTML content in cache.
     */
    public function storeHtml(string $key, string $html): void
    {
        if (! Config::cacheViewsEnabled()) {
            return;
        }

        $this->htmlCache->put($key, $html, Config::cacheViewsTtl());
    }

    /**
     * Store image content in cache.
     */
    public function storeImage(string $key, string $image): void
    {
        if (! Config::cacheImagesEnabled()) {
            return;
        }

        $this->imageCache->put($key, $image, Config::cacheImagesTtl());
    }
}
