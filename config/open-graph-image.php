<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Browsershot Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure various Browsershot options that control how
    | HTML content is converted into images. These settings will be passed
    | to the underlying Browsershot instance during image generation.
    |
    */

    'browsershot' => [
        'timeout' => (int) env('OPEN_GRAPH_IMAGE_BROWSERSHOT_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | These settings determine the caching behavior for generated Open Graph
    | images and rendered views. You may specify different cache stores and
    | TTL values for images and views to optimize performance.
    |
    */

    'cache' => [
        'images' => [
            'enabled' => env('OPEN_GRAPH_IMAGE_CACHE_IMAGES_ENABLED', false),
            'store' => env('OPEN_GRAPH_IMAGE_CACHE_IMAGES_STORE', 'file'),
            'ttl' => (int) env('OPEN_GRAPH_IMAGE_CACHE_IMAGES_TTL', 604800),
        ],

        'views' => [
            'enabled' => env('OPEN_GRAPH_IMAGE_CACHE_VIEWS_ENABLED', false),
            'store' => env('OPEN_GRAPH_IMAGE_CACHE_VIEWS_STORE', 'file'),
            'ttl' => (int) env('OPEN_GRAPH_IMAGE_CACHE_VIEWS_TTL', 3600),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CSS Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify the CSS configuration for styling your Open Graph
    | images. These settings control how your generated images will be
    | visually styled and formatted.
    |
    */

    'css' => [
        'path' => env('OPEN_GRAPH_IMAGE_CSS_PATH', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Dimensions
    |--------------------------------------------------------------------------
    |
    | These values determine the default dimensions for generated Open Graph
    | images. The standard recommended size for Open Graph images is
    | 1200x630 pixels, which provides optimal display across platforms.
    |
    */

    'dimensions' => [
        'height' => 630,
        'width' => 1200,
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | These settings configure the HTTP routes that your Open Graph image
    | generator will respond to. You may customize the path, prefix, and
    | route names to fit your application's routing conventions.
    |
    */

    'route' => [
        'name' => env('OPEN_GRAPH_IMAGE_ROUTE_NAME', 'open-graph-image'),
        'path' => env('OPEN_GRAPH_IMAGE_ROUTE_PATH', 'open-graph-image'),
        'prefix' => env('OPEN_GRAPH_IMAGE_ROUTE_PREFIX', ''),

        'preview' => [
            'enabled' => env('OPEN_GRAPH_IMAGE_ROUTE_PREVIEW_ENABLED', env('APP_ENV') === 'local'),
            'name' => env('OPEN_GRAPH_IMAGE_ROUTE_PREVIEW_NAME', 'open-graph-image.preview'),
            'path' => env('OPEN_GRAPH_IMAGE_ROUTE_PREVIEW_PATH', 'open-graph-image/preview'),
        ],
    ],

];
