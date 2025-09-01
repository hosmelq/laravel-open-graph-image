<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\CSS;

use function Safe\file_get_contents;
use function Safe\filemtime;

use HosmelQ\OpenGraphImage\Contracts\ModificationTimeResolver;
use HosmelQ\OpenGraphImage\Support\Config;
use Safe\Exceptions\FilesystemException;

class CSSResolver implements ModificationTimeResolver
{
    /**
     * Get the CSS file content.
     *
     * @throws FilesystemException
     */
    public function getContent(): string
    {
        return file_get_contents($this->getPath());
    }

    /**
     * Get the CSS file modification time.
     *
     * @throws FilesystemException
     */
    public function getModificationTime(): int
    {
        return filemtime($this->getPath());
    }

    /**
     * Get the path to the CSS file.
     */
    public function getPath(): string
    {
        $path = Config::cssPath();

        if ($path !== '' && file_exists($path)) {
            return $path;
        }

        $path = resource_path('vendor/open-graph-image/css/open-graph-image.css');

        if (file_exists($path)) {
            return $path;
        }

        return __DIR__.'/../../resources/css/open-graph-image.css';
    }
}
