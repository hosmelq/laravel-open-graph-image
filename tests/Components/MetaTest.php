<?php

declare(strict_types=1);

it('renders meta tags', function (): void {
    $view = $this->blade(
        '<x-open-graph-image-meta :data="$data" :template="$template" />',
        [
            'data' => [],
            'template' => 'example',
        ]
    );

    expect($view)->toMatchSnapshot();
});
