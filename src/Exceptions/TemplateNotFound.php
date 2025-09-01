<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Exceptions;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class TemplateNotFound extends Exception
{
    /**
     * Create an exception for a missing template.
     */
    public static function create(string $template): self
    {
        return new self(sprintf("Template '%s' not found.", $template));
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(): bool|SymfonyResponse
    {
        if (Config::boolean('app.debug')) {
            return false;
        }

        return Response::make('Template not found.', 404);
    }
}
