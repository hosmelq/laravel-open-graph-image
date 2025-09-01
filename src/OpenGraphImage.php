<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage;

use HosmelQ\OpenGraphImage\Exceptions\TemplateNotFound;
use HosmelQ\OpenGraphImage\Rendering\ImageRenderer;
use HosmelQ\OpenGraphImage\Support\Config;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\Factory as ViewFactory;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class OpenGraphImage implements Htmlable, Responsable
{
    /**
     * Data to pass to the template for rendering.
     *
     * @var array<string, mixed>
     */
    private array $data = [];

    /**
     * Name of the template to render.
     */
    private string $template;

    /**
     * Create a new Open Graph image instance.
     */
    public function __construct(
        private readonly ImageRenderer $renderer,
        private readonly ViewFactory $viewFactory,
    ) {
    }

    /**
     * Generate the Open Graph image.
     *
     * @throws InvalidArgumentException
     */
    public function generate(): string
    {
        return $this->renderer->image($this->template, $this->data);
    }

    /**
     * Set the template name.
     *
     * @throws TemplateNotFound
     */
    public function template(string $template): self
    {
        $view = 'open-graph-image.templates.'.$template;

        if (! $this->viewFactory->exists($view)) {
            throw TemplateNotFound::create($template);
        }

        $this->template = $template;

        return $this;
    }

    /**
     * Convert the Open Graph image to HTML.
     */
    public function toHtml(): string
    {
        return $this->renderer->html($this->template, $this->data);
    }

    /**
     * Convert the Open Graph image to an HTTP response.
     *
     * @param Request $request
     *
     * @throws InvalidArgumentException
     */
    public function toResponse($request): SymfonyResponse // @phpstan-ignore-line typeCoverage.paramTypeCoverage
    {
        $image = $this->generate();

        return Response::make($image, 200, [
            'Cache-Control' => sprintf(
                'public, immutable, no-transform, max-age=%1$d, s-maxage=%1$d',
                Config::cacheImagesTtl()
            ),
            'Content-Length' => strlen($image),
            'Content-Type' => 'image/png',
            'ETag' => hash('sha256', $image),
        ]);
    }

    /**
     * Set data to pass to the template.
     *
     * @param array<string, mixed>|string $key
     */
    public function with(array|string $key, mixed $value = null): self // @phpstan-ignore-line typePerfect.narrowPublicClassMethodParamType
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }
}
