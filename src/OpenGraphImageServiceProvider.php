<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage;

use HosmelQ\OpenGraphImage\Cache\CacheKeysGenerator;
use HosmelQ\OpenGraphImage\Cache\RenderingCacheManager;
use HosmelQ\OpenGraphImage\Components\Layout;
use HosmelQ\OpenGraphImage\Components\Meta;
use HosmelQ\OpenGraphImage\Console\Commands\DownloadTailwindCommand;
use HosmelQ\OpenGraphImage\Contracts\CacheKeys;
use HosmelQ\OpenGraphImage\Contracts\Screenshotter;
use HosmelQ\OpenGraphImage\Rendering\Screenshot\BrowsershotScreenshotter;
use HosmelQ\OpenGraphImage\Support\Config;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OpenGraphImageServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package assets and commands.
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-open-graph-image')
            ->hasCommand(DownloadTailwindCommand::class)
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->askToStarRepoOnGitHub('hosmelq/laravel-open-graph-image')
                    ->publishConfigFile()
                    ->startWith(function (InstallCommand $command): void {
                        $command->call('open-graph-image:download-tailwind-binary');
                    });
            })
            ->hasRoute('web')
            ->hasViewComponent('open-graph-image', Layout::class)
            ->hasViewComponent('open-graph-image', Meta::class)
            ->hasViews();
    }

    /**
     * Bootstrap package services.
     */
    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__.'/../resources/css/open-graph-image.css' => resource_path('vendor/open-graph-image/css/open-graph-image.css'),
        ], 'open-graph-image-css');

        Blade::directive('embedImage', function (string $expression): string {
            return sprintf('<?php echo \HosmelQ\OpenGraphImage\image_to_data_uri(%s); ?>', $expression);
        });
    }

    /**
     * Register package services.
     */
    public function packageRegistered(): void
    {
        $this->app->bind(CacheKeys::class, CacheKeysGenerator::class);

        $this->app->singleton(RenderingCacheManager::class, function (Application $app): RenderingCacheManager {
            return new RenderingCacheManager(
                $app->make('cache')->store(Config::cacheViewsStore()),
                $app->make('cache')->store(Config::cacheImagesStore()),
            );
        });

        $this->app->singleton(Screenshotter::class, BrowsershotScreenshotter::class);
    }
}
