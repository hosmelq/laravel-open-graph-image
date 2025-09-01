<?php

declare(strict_types=1);

use function HosmelQ\OpenGraphImage\image_to_data_uri;

it('returns empty string when file does not exist', function (): void {
    $result = image_to_data_uri('/path/to/nonexistent/file.png');

    expect($result)->toBe('');
});

it('converts image to data URI', function (string $ext, string $path): void {
    $result = image_to_data_uri($path);

    expect($result)->toStartWith(sprintf('data:image/%s;base64,', $ext));

    $decodedContent = base64_decode(explode(',', $result)[1], true);
    $originalContent = file_get_contents($path);

    expect($decodedContent)->toBe($originalContent);
})->with([
    'jpg' => [
        'ext' => 'jpeg',
        'path' => __DIR__.'/TestSupport/testfiles/image_0_01MB.jpg',
    ],
    'png' => [
        'ext' => 'png',
        'path' => __DIR__.'/TestSupport/testfiles/image_0_01MB.png',
    ],
]);
