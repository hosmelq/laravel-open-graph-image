<?php

declare(strict_types=1);

use HosmelQ\OpenGraphImage\Cache\RenderingCacheManager;

it('stores and retrieves HTML when caching is enabled', function (): void {
    config()->set([
        'open-graph-image.cache.views.enabled' => true,
        'open-graph-image.cache.views.ttl' => 3600,
    ]);

    $manager = resolve(RenderingCacheManager::class);

    $manager->storeHtml('test-key', '<h1>Hello World</h1>');

    expect($manager->getHtml('test-key'))->toBe('<h1>Hello World</h1>')
        ->and($manager->getHtml('non-existent-key'))->toBeNull();
});

it('returns null for HTML when caching is disabled', function (): void {
    config()->set(['open-graph-image.cache.views.enabled' => false]);

    $manager = resolve(RenderingCacheManager::class);

    $manager->storeHtml('test-key', '<h1>Hello</h1>');

    expect($manager->getHtml('test-key'))->toBeNull();
});

it('stores and retrieves images when caching is enabled', function (): void {
    config()->set([
        'open-graph-image.cache.images.enabled' => true,
        'open-graph-image.cache.images.ttl' => 7200,
    ]);

    $manager = resolve(RenderingCacheManager::class);

    $manager->storeImage('image-key', 'fake-png-binary-data');

    expect($manager->getImage('image-key'))->toBe('fake-png-binary-data')
        ->and($manager->getImage('non-existent-image'))->toBeNull();
});

it('returns null for images when caching is disabled', function (): void {
    config()->set(['open-graph-image.cache.images.enabled' => false]);

    $manager = resolve(RenderingCacheManager::class);

    $manager->storeImage('image-key', 'image-data');

    expect($manager->getImage('image-key'))->toBeNull();
});
