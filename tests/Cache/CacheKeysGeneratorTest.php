<?php

declare(strict_types=1);

use HosmelQ\OpenGraphImage\Contracts\CacheKeys;
use HosmelQ\OpenGraphImage\CSS\CSSResolver;
use HosmelQ\OpenGraphImage\Exceptions\TemplateNotFound;
use HosmelQ\OpenGraphImage\Templates\TemplateModificationTimeResolver;
use Mockery\MockInterface;
use Safe\Exceptions\JsonException;

it('generates deterministic cache keys with proper prefixes', function (): void {
    $this->mock(CSSResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->andReturn(123);
    });
    $this->mock(TemplateModificationTimeResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->with('post')
            ->andReturn(456);
    });

    $data = ['title' => 'Test Title', 'description' => 'Test Description'];
    $generator = resolve(CacheKeys::class);

    $htmlKey = $generator->html('post', $data);
    $imageKey = $generator->image('post', $data);

    $hash = hash('sha256', json_encode([
        'template' => 'post',
        'compiled_at' => 456,
        'css_at' => 123,
        'data' => ['description' => 'Test Description', 'title' => 'Test Title'],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    expect($htmlKey)
        ->toBe('open_graph_html_'.$hash)
        ->toStartWith('open_graph_html_')
        ->and($imageKey)->toBe('open_graph_image_'.$hash)
        ->toStartWith('open_graph_image_');
});

it('invalidates cache when CSS file changes', function (): void {
    $this->mock(CSSResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->andReturn(1, 2);
    });
    $this->mock(TemplateModificationTimeResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->with('post')
            ->andReturn(1);
    });

    $generator = resolve(CacheKeys::class);

    $firstKey = $generator->html('post', ['title' => 'Test']);
    $secondKey = $generator->html('post', ['title' => 'Test']);

    expect($firstKey)->not()->toBe($secondKey);
});

it('invalidates cache when template file changes', function (): void {
    $this->mock(CSSResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->andReturn(1);
    });
    $this->mock(TemplateModificationTimeResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->with('post')
            ->andReturn(1, 2);
    });

    $generator = resolve(CacheKeys::class);

    $firstKey = $generator->image('post', ['title' => 'Test']);
    $secondKey = $generator->image('post', ['title' => 'Test']);

    expect($firstKey)->not()->toBe($secondKey);
});

it('produces identical keys regardless of data order', function (): void {
    $this->mock(CSSResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->andReturn(55);
    });
    $this->mock(TemplateModificationTimeResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->with('post')
            ->andReturn(66);
    });

    $generator = resolve(CacheKeys::class);

    $orderedData = ['author' => 'John', 'title' => 'First Post', 'description' => 'Lorem ipsum'];
    $shuffledData = ['description' => 'Lorem ipsum', 'author' => 'John', 'title' => 'First Post'];

    $firstKey = $generator->image('post', $orderedData);
    $secondKey = $generator->image('post', $shuffledData);

    expect($firstKey)->toBe($secondKey);
});

it('creates unique keys for different templates', function (): void {
    $this->mock(CSSResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->andReturn(5);
    });

    $this->mock(TemplateModificationTimeResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->with('post')
            ->andReturn(10);
        $mock->shouldReceive('getModificationTime')
            ->with('article')
            ->andReturn(10);
    });

    $generator = resolve(CacheKeys::class);

    $firstKey = $generator->html('post', ['title' => 'Test']);
    $secondKey = $generator->html('article', ['title' => 'Test']);

    expect($firstKey)->not()->toBe($secondKey);
});

it('creates unique keys for different data values', function (): void {
    $this->mock(CSSResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->andReturn(7);
    });
    $this->mock(TemplateModificationTimeResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->with('post')
            ->andReturn(8);
    });

    $generator = resolve(CacheKeys::class);

    $firstKey = $generator->image('post', ['title' => 'First Post']);
    $secondKey = $generator->image('post', ['title' => 'Second Post']);

    expect($firstKey)->not()->toBe($secondKey);
});

it('throws JsonException for non-serializable data', function (): void {
    $this->mock(CSSResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->andReturn(1);
    });
    $this->mock(TemplateModificationTimeResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->with('post')
            ->andReturn(1);
    });

    $generator = resolve(CacheKeys::class);

    expect($generator->html('post', ['invalid' => INF]));
})->throws(JsonException::class, 'Inf and NaN cannot be JSON encoded');

it('throws TemplateNotFoundException for missing templates', function (): void {
    $this->mock(CSSResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->andReturn(1);
    });
    $this->mock(TemplateModificationTimeResolver::class, function (MockInterface $mock): void {
        $mock->shouldReceive('getModificationTime')
            ->with('non-existent')
            ->andThrow(new TemplateNotFound('Template not found.'));
    });

    $generator = resolve(CacheKeys::class);

    $generator->image('non-existent', []);
})->throws(TemplateNotFound::class, 'Template not found.');
