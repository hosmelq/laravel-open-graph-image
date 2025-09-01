<?php

declare(strict_types=1);

use HosmelQ\OpenGraphImage\Cache\RenderingCacheManager;
use HosmelQ\OpenGraphImage\Contracts\CacheKeys;
use HosmelQ\OpenGraphImage\Rendering\ImageRenderer;
use Mockery\MockInterface;

it('returns cached html without re-rendering', function (): void {
    $cacheKeys = $this->mock(CacheKeys::class, function (MockInterface $mock): void {
        $mock->shouldReceive('html')
            ->with('post', [])
            ->andReturn('test-cache-key');
    });

    $cacheManager = $this->mock(RenderingCacheManager::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getHtml')
            ->with('test-cache-key')
            ->andReturn('<cached>HTML content</cached>');

        $mock->shouldNotReceive('storeHtml');
    });

    $renderer = resolve(ImageRenderer::class);

    $html = $renderer->html('post', []);

    expect($html)->toBe('<cached>HTML content</cached>');
});

it('returns cached image without re-rendering', function (): void {
    $cacheKeys = $this->mock(CacheKeys::class, function (MockInterface $mock): void {
        $mock->shouldReceive('image')
            ->with('post', ['title' => 'Test Title'])
            ->andReturn('image-cache-key');
    });

    $cacheManager = $this->mock(RenderingCacheManager::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getImage')
            ->with('image-cache-key')
            ->andReturn('fake-png-binary-data');

        $mock->shouldNotReceive('storeImage');
    });

    $renderer = resolve(ImageRenderer::class);

    $image = $renderer->image('post', ['title' => 'Test Title']);

    expect($image)->toBe('fake-png-binary-data');
});
