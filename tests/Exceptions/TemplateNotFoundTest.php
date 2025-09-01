<?php

declare(strict_types=1);

use HosmelQ\OpenGraphImage\Exceptions\TemplateNotFound;
use Symfony\Component\HttpFoundation\Response;

it('returns false when rendering in debug mode', function (): void {
    config(['app.debug' => true]);

    $exception = new TemplateNotFound('Test exception');

    expect($exception->render())->toBeFalse();
});

it('returns 404 response when rendering in non-debug mode', function (): void {
    config(['app.debug' => false]);

    $exception = new TemplateNotFound('Test exception');
    $response = $exception->render();

    expect($response)->toBeInstanceOf(Response::class)
        ->getStatusCode()->toBe(404);
});
