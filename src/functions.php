<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage;

use function Safe\file_get_contents;
use function Safe\mime_content_type;

use Safe\Exceptions\FileinfoException;
use Safe\Exceptions\FilesystemException;

/**
 * Convert an image to a data URI.
 *
 * @throws FilesystemException
 * @throws FileinfoException
 */
function image_to_data_uri(string $path): string
{
    if (! file_exists($path)) {
        return '';
    }

    return sprintf(
        'data:%s;base64,%s',
        mime_content_type($path),
        base64_encode(file_get_contents($path))
    );
}
