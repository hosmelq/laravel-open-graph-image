<?php

declare(strict_types=1);

use HosmelQ\OpenGraphImage\Http\Controllers\PreviewController;
use HosmelQ\OpenGraphImage\Http\Controllers\RenderController;
use HosmelQ\OpenGraphImage\Support\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Open Graph Image Routes
|--------------------------------------------------------------------------
|
| These routes handle Open Graph image generation and optional preview functionality.
| The routes use configurable names and paths from the config file.
|
*/

Route::prefix(Config::routePrefix())->group(function () {
    Route::get(Config::routePath(), RenderController::class)
        ->name(Config::routeName());

    if (Config::routePreviewEnabled()) {
        Route::get(Config::routePreviewPath(), PreviewController::class)
            ->name(Config::routePreviewName());
    }
});
