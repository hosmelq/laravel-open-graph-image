<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \HosmelQ\OpenGraphImage\OpenGraphImage template(string $template)
 */
class OpenGraphImage extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return \HosmelQ\OpenGraphImage\OpenGraphImage::class;
    }
}
