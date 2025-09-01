<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;
    use WithWorkbench;

    /**
     * {@inheritDoc}
     */
    protected function defineEnvironment($app): void
    {
        tap($app['config'], function (Repository $config): void {
            $config->set('open-graph-image.route.preview.enabled', true);

            $config->set('view.paths', [__DIR__.'/TestSupport/resources/views']);
        });
    }
}
