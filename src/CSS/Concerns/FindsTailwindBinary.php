<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\CSS\Concerns;

use Symfony\Component\Process\ExecutableFinder;

trait FindsTailwindBinary
{
    /**
     * Find the Tailwind binary path.
     */
    protected function findTailwindBinary(): null|string
    {
        return (new ExecutableFinder())->find('tailwindcss-cli', null, [base_path()]);
    }
}
