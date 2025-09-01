<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Http\Controllers;

use HosmelQ\OpenGraphImage\Facades\OpenGraphImage;
use Illuminate\Http\Request;

readonly class RenderController
{
    /**
     * Render the OG image from request parameters.
     */
    public function __invoke(Request $request): \HosmelQ\OpenGraphImage\OpenGraphImage
    {
        /** @var array<string, mixed> $data */
        $data = collect($request->query())->except(['template'])->toArray();
        $template = (string) $request->string('template');

        return OpenGraphImage::template($template)->with($data);
    }
}
