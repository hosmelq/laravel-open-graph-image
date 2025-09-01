<?php

declare(strict_types=1);

use HosmelQ\OpenGraphImage\OpenGraphImage;
use HosmelQ\OpenGraphImage\Rendering\ImageRenderer;
use Illuminate\View\Factory as ViewFactory;
use Mockery\MockInterface;

it('merges data when with() receives an array', function (): void {
    $renderer = $this->mock(ImageRenderer::class, function (MockInterface $mock): void {
        $mock->shouldReceive('html')
            ->once()
            ->with('post', [
                'title' => 'Updated Title',
                'description' => 'Test Description',
                'author' => 'John Doe',
            ])
            ->andReturn('<html>test</html>');
    });

    $viewFactory = $this->mock(ViewFactory::class, function (MockInterface $mock): void {
        $mock->shouldReceive('exists')
            ->with('open-graph-image.templates.post')
            ->andReturn(true);
    });

    $ogImage = new OpenGraphImage($renderer, $viewFactory);

    $ogImage->template('post')
        ->with('title', 'Initial Title')
        ->with(['title' => 'Updated Title', 'description' => 'Test Description'])
        ->with('author', 'John Doe')
        ->toHtml();
});
