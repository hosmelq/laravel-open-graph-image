<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    /**
     * {@inheritDoc}
     */
    public function render(): View
    {
        return view('open-graph-image::components.layout');
    }
}
