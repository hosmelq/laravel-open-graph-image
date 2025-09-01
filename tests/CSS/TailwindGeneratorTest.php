<?php

declare(strict_types=1);

use HosmelQ\OpenGraphImage\CSS\CSSResolver;
use HosmelQ\OpenGraphImage\CSS\TailwindGenerator;
use HosmelQ\OpenGraphImage\Exceptions\TailwindBinaryNotFound;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    $binaryPath = base_path('tailwindcss-cli');

    File::delete($binaryPath);
});

afterEach(function (): void {
    $binaryPath = base_path('tailwindcss-cli');

    File::delete($binaryPath);
});

it('throws TailwindBinaryNotFound when binary does not exist', function (): void {
    $generator = resolve(TailwindGenerator::class);

    $generator->generate('<div>Test</div>');
})->throws(
    TailwindBinaryNotFound::class,
    'Tailwind CLI binary not found. Run `php artisan open-graph-image:download-tailwind-binary` to download it.'
);

it('generates CSS from HTML content', function (): void {
    File::copy(
        __DIR__.'/../TestSupport/bin/tailwindcss-cli',
        base_path('tailwindcss-cli')
    );
    File::chmod(base_path('tailwindcss-cli'), 0755);

    $generator = resolve(TailwindGenerator::class);

    $css = $generator->generate('<div>Test</div>');

    expect($css)
        ->not->toBeEmpty()
        ->toContain('Mock Tailwind CSS Output');
});

it('handles process errors gracefully', function (): void {
    File::copy(
        __DIR__.'/../TestSupport/bin/tailwindcss-cli',
        base_path('tailwindcss-cli')
    );
    File::chmod(base_path('tailwindcss-cli'), 0755);

    $cssResolver = Mockery::mock(CSSResolver::class);
    $cssResolver->shouldReceive('getContent')
        ->once()
        ->andReturn('trigger-error { color: red; }');

    $generator = new TailwindGenerator($cssResolver);

    $generator->generate('<div>Test</div>');
})->throws(RuntimeException::class, 'Tailwind CSS compilation failed: ');
