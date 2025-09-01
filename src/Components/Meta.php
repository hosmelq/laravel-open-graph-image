<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Components;

use HosmelQ\OpenGraphImage\Support\Config;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Meta extends Component
{
    /**
     * Create a new meta component instance.
     *
     * @param array<string, mixed> $data
     */
    public function __construct(public string $template, public array $data = [])
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render(): View
    {
        return view('open-graph-image::components.meta')->with([
            'height' => Config::dimensionsHeight(),
            'image_url' => route(Config::routeName(), array_merge($this->data, [
                'template' => $this->template,
            ])),
            'width' => Config::dimensionsWidth(),
        ]);
    }
}
