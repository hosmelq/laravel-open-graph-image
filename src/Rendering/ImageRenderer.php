<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Rendering;

use HosmelQ\OpenGraphImage\Cache\RenderingCacheManager;
use HosmelQ\OpenGraphImage\Contracts\CacheKeys;
use HosmelQ\OpenGraphImage\Contracts\Screenshotter;
use HosmelQ\OpenGraphImage\CSS\TailwindGenerator;
use HosmelQ\OpenGraphImage\Exceptions\TailwindBinaryNotFound;
use HosmelQ\OpenGraphImage\Support\Config;
use Illuminate\View\Factory;
use Safe\Exceptions\FilesystemException;

class ImageRenderer
{
    /**
     * Create a new image renderer instance.
     */
    public function __construct(
        protected CacheKeys $cacheKeys,
        protected Factory $viewFactory,
        protected RenderingCacheManager $cacheManager,
        protected Screenshotter $screenshotter,
        protected TailwindGenerator $tailwindGenerator,
    ) {
    }

    /**
     * Render HTML with embedded CSS.
     *
     * @param array<string, mixed> $data
     *
     * @throws TailwindBinaryNotFound
     * @throws FilesystemException
     */
    public function html(string $template, array $data = []): string
    {
        $cacheKey = $this->cacheKeys->html($template, $data);

        if (! is_null($cachedHtml = $this->cacheManager->getHtml($cacheKey))) {
            return $cachedHtml;
        }

        $html = $this->renderView($template, $data);
        $css = $this->tailwindGenerator->generate($html);
        $html = $this->injectCSS($html, $css);

        $this->cacheManager->storeHtml($cacheKey, $html);

        return $html;
    }

    /**
     * Render the OG image.
     *
     * @param array<string, mixed> $data
     *
     * @throws TailwindBinaryNotFound
     * @throws FilesystemException
     */
    public function image(string $template, array $data = []): string
    {
        $cacheKey = $this->cacheKeys->image($template, $data);

        if (! is_null($cachedImage = $this->cacheManager->getImage($cacheKey))) {
            return $cachedImage;
        }

        $image = $this->screenshotter->capture(
            $this->html($template, $data),
            Config::dimensionsWidth(),
            Config::dimensionsHeight()
        );

        $this->cacheManager->storeImage($cacheKey, $image);

        return $image;
    }

    /**
     * Inject the generated CSS styles into the HTML template.
     */
    private function injectCSS(string $html, string $css): string
    {
        return str_replace('{IMAGE_TEMPLATE_STYLES}', $css, $html);
    }

    /**
     * Render the view template.
     *
     * @param array<string, mixed> $data
     */
    private function renderView(string $template, array $data = []): string
    {
        $view = 'open-graph-image.templates.'.$template;

        return $this->viewFactory->make($view, $data)->render();
    }
}
