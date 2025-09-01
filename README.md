# Laravel Open Graph Image

Generate dynamic Open Graph images for Laravel applications using Blade templates and Tailwind CSS.

See it in action: a real Open Graph image generated from a Blade + Tailwind template.

![OG image example](https://laravel-open-graph-image.hosmelq.com/open-graph-image?template=demo)

Source: https://laravel-open-graph-image.hosmelq.com/open-graph-image?template=demo

## Introduction

Using this package, you can generate Open Graph images from Blade templates styled with Tailwind CSS. Whether you want to create preview images for blog posts, product cards, or event announcements, this package handles it all.

```php
{{-- In your Blade template --}}
<x-open-graph-image-meta 
    template="post"
    :data="[
        'author' => $post->author->name,
        'title' => $post->title,
    ]"
/>
```

The image is generated when accessed and cached for subsequent requests.

## Requirements

- PHP 8.2+
- Laravel 12+
- Node.js and npm
- PHP extension ext-fileinfo (required for @embedImage)

## Installation & setup

You can install the package via composer:

```bash
composer require hosmelq/laravel-open-graph-image
```

### Publishing the config file

Optionally, you can publish the config file with this command:

```bash
php artisan vendor:publish --tag="open-graph-image-config"
```

<details>
<summary>View the published config file.</summary>

```php
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
```
</details>

### Configuring Browsershot

This package uses Browsershot for screenshot generation. For detailed requirements and installation instructions, see the [Browsershot requirements](https://spatie.be/docs/browsershot/v4/requirements).

To get started, you'll need to install Puppeteer:

```bash
npm install puppeteer
```

### Downloading Tailwind CLI

Download the Tailwind CSS v4 binary:

```bash
php artisan open-graph-image:download-tailwind-binary
```

## Basic usage

### Getting started

Let's generate an Open Graph image for a blog post. We'll create a Blade template and add the meta component to your page.

First, create a template in `resources/views/open-graph-image/templates/post.blade.php`:

```blade
<x-open-graph-image-layout>
    <div class="flex h-screen items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
        <div class="mx-auto max-w-4xl p-12 text-center">
            <h1 class="mb-4 text-6xl font-bold text-white">
                {{ $title }}
            </h1>
            <p class="text-2xl text-white/90">
                By {{ $author }} • {{ $date }}
            </p>
        </div>
    </div>
</x-open-graph-image-layout>
```

Now, add the meta component to your page:

```blade
{{-- In your layout or page head --}}
<x-open-graph-image-meta 
    template="post" 
    :data="[
        'title' => $post->title,
        'author' => $post->author->name,
        'date' => $post->published_at->format('F j, Y')
    ]"
/>
```

The meta component outputs `og:image`, `og:image:width`, `og:image:height`, `og:image:type`, `twitter:card`, and `twitter:image` meta tags. The image is generated when accessed and cached for subsequent requests.

### Programmatically generating images

Generate images directly in your code:

```php
use HosmelQ\OpenGraphImage\Facades\OpenGraphImage;

$image = OpenGraphImage::template('post')
    ->with([
        'title' => 'Getting Started with Laravel',
        'author' => 'Jane Smith',
        'date' => 'December 1, 2024'
    ])
    ->generate(); // Returns the PNG image as a string
```

### Using the preview mode

During development, preview your templates as HTML without generating images:

```
http://your-app.test/open-graph-image/preview?template=post&title=Test&author=John
```

The preview route is only available in the local environment by default.

> [!TIP]
> Use your browser's Responsive Mode with a 1200×630 viewport to preview templates at the exact Open Graph image dimensions. This speeds up design iteration without regenerating PNGs.

## Creating templates

Templates are Blade views stored in `resources/views/open-graph-image/templates/`.

### Simple template

```blade
{{-- resources/views/open-graph-image/templates/simple.blade.php --}}
<x-open-graph-image-layout>
    <div class="flex h-screen items-center justify-center bg-white">
        <h1 class="text-5xl font-bold">{{ $text }}</h1>
    </div>
</x-open-graph-image-layout>
```

### Demo template

```blade
{{-- resources/views/open-graph-image/templates/demo.blade.php --}}
<x-open-graph-image-layout>
    <div class="flex h-screen items-center justify-center bg-gray-950 p-20 text-white">
        <div class="flex flex-col gap-y-16">
            <div class="flex flex-col gap-y-4">
                <div class="text-4xl">Laravel Open Graph Image</div>
                <div class="text-6xl leading-tight text-gray-400">
                    Generate Open Graph images for your<br>
                    <span class="text-sky-400">Laravel</span> application.
                </div>
            </div>
            <div class="self-start rounded-xl bg-white/5 p-1 inset-ring inset-ring-white/10">
                <div class="px-3 pt-0.5 pb-1.5 text-lg text-white/50">
                    Terminal
                </div>
                <div class="rounded-lg bg-white/5 p-5 font-mono text-xl">
                    $ composer require hosmelq/laravel-open-graph-image
                </div>
            </div>
        </div>
    </div>
</x-open-graph-image-layout>
```

### Using the image embedding directive

The `@embedImage` directive converts local images to data URIs. Use paths like `public_path('images/logo.png')`:

```blade
{{-- Embed a local image as a data URI --}}
<img src="@embedImage(public_path('images/logo.png'))">
```

## Customizing CSS

The package processes your templates with Tailwind CSS v4. To use custom CSS, publish the default file:

```bash
php artisan vendor:publish --tag="open-graph-image-css"
```

This creates `resources/vendor/open-graph-image/css/open-graph-image.css` that you can modify:

```css
@import "tailwindcss";

.gradient-text {
    @apply bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent;
}
```

Alternatively, use your own CSS file from any location:

```php
// .env
OPEN_GRAPH_IMAGE_CSS_PATH=/path/to/your/custom.css
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Hosmel Quintana](https://github.com/hosmelq)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
