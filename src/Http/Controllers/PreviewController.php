<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Http\Controllers;

use HosmelQ\OpenGraphImage\Facades\OpenGraphImage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

readonly class PreviewController
{
    /**
     * Return an HTML preview of the OG image template.
     */
    public function __invoke(Request $request): Response
    {
        /** @var array<string, mixed> $data */
        $data = collect($request->query())->except(['template'])->toArray();
        $template = (string) $request->string('template');

        return new Response(OpenGraphImage::template($template)->with($data)->toHtml());
    }
}
