<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Support;

use Illuminate\Support\Facades\Config as ConfigFacade;

class Config
{
    /**
     * Get the Browsershot timeout value.
     */
    public static function browsershotTimeout(): int
    {
        return ConfigFacade::integer('open-graph-image.browsershot.timeout');
    }

    /**
     * Check if image caching is enabled.
     */
    public static function cacheImagesEnabled(): bool
    {
        return ConfigFacade::boolean('open-graph-image.cache.images.enabled');
    }

    /**
     * Get the cache store for images.
     */
    public static function cacheImagesStore(): string
    {
        return ConfigFacade::string('open-graph-image.cache.images.store');
    }

    /**
     * Get the TTL for cached images in seconds.
     */
    public static function cacheImagesTtl(): int
    {
        return ConfigFacade::integer('open-graph-image.cache.images.ttl');
    }

    /**
     * Check if view caching is enabled.
     */
    public static function cacheViewsEnabled(): bool
    {
        return ConfigFacade::boolean('open-graph-image.cache.views.enabled');
    }

    /**
     * Get the cache store for views.
     */
    public static function cacheViewsStore(): string
    {
        return ConfigFacade::string('open-graph-image.cache.views.store');
    }

    /**
     * Get the TTL for cached views in seconds.
     */
    public static function cacheViewsTtl(): int
    {
        return ConfigFacade::integer('open-graph-image.cache.views.ttl');
    }

    /**
     * Get the CSS file path.
     */
    public static function cssPath(): string
    {
        return ConfigFacade::string('open-graph-image.css.path');
    }

    /**
     * Get the image height dimension.
     */
    public static function dimensionsHeight(): int
    {
        return ConfigFacade::integer('open-graph-image.dimensions.height');
    }

    /**
     * Get the image width dimension.
     */
    public static function dimensionsWidth(): int
    {
        return ConfigFacade::integer('open-graph-image.dimensions.width');
    }

    /**
     * Get the route name.
     */
    public static function routeName(): string
    {
        return ConfigFacade::string('open-graph-image.route.name');
    }

    /**
     * Get the route path.
     */
    public static function routePath(): string
    {
        return ConfigFacade::string('open-graph-image.route.path');
    }

    /**
     * Get the route prefix.
     */
    public static function routePrefix(): string
    {
        return ConfigFacade::string('open-graph-image.route.prefix');
    }

    /**
     * Check if the preview route is enabled.
     */
    public static function routePreviewEnabled(): bool
    {
        return ConfigFacade::boolean('open-graph-image.route.preview.enabled');
    }

    /**
     * Get the preview route name.
     */
    public static function routePreviewName(): string
    {
        return ConfigFacade::string('open-graph-image.route.preview.name');
    }

    /**
     * Get the preview route path.
     */
    public static function routePreviewPath(): string
    {
        return ConfigFacade::string('open-graph-image.route.preview.path');
    }
}
