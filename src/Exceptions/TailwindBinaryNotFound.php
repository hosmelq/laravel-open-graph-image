<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Exceptions;

use Exception;

class TailwindBinaryNotFound extends Exception
{
    /**
     * Create an exception for a missing Tailwind CLI binary.
     */
    public static function create(): self
    {
        return new self(
            'Tailwind CLI binary not found. Run `php artisan open-graph-image:download-tailwind-binary` to download it.'
        );
    }
}
