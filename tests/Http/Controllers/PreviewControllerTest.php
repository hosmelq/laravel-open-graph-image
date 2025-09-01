<?php

declare(strict_types=1);

use function Pest\Laravel\get;
use function Pest\Laravel\withoutExceptionHandling;

use HosmelQ\OpenGraphImage\CSS\TailwindGenerator;
use HosmelQ\OpenGraphImage\Exceptions\TemplateNotFound;
use HosmelQ\OpenGraphImage\Support\Config;
use Mockery\MockInterface;

beforeEach(function (): void {
    $this->mock(TailwindGenerator::class, function (MockInterface $mock): void {
        $mock->shouldReceive('generate')
            ->andReturn('/* mocked tailwind css */');
    });
});

it('returns 404 for non-existent template when debug is disabled', function (): void {
    config(['app.debug' => false]);

    $response = get(route(Config::routePreviewName(), [
        'template' => 'non-existent-template',
    ]));

    $response->assertNotFound();
});

it('throws TemplateNotFound exception when debug is enabled', function (): void {
    config(['app.debug' => true]);

    withoutExceptionHandling();

    get(route(Config::routePreviewName(), [
        'template' => 'non-existent-template',
    ]));
})->throws(TemplateNotFound::class, "Template 'non-existent-template' not found.");

it('renders template', function (): void {
    $response = get(route(Config::routePreviewName(), [
        'description' => 'Custom Description',
        'template' => 'post',
        'title' => 'Custom Title',
    ]));

    expect($response)->toMatchSnapshot();
});
