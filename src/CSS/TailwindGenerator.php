<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\CSS;

use function Safe\file_get_contents;
use function Safe\file_put_contents;

use HosmelQ\OpenGraphImage\CSS\Concerns\FindsTailwindBinary;
use HosmelQ\OpenGraphImage\Exceptions\TailwindBinaryNotFound;
use RuntimeException;
use Safe\Exceptions\FilesystemException;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Process\Process;

class TailwindGenerator
{
    use FindsTailwindBinary;

    /**
     * Create a new Tailwind generator instance.
     */
    public function __construct(
        protected CSSResolver $cssResolver,
    ) {
    }

    /**
     * Generate Tailwind CSS from HTML content.
     *
     * @throws TailwindBinaryNotFound
     * @throws FilesystemException
     */
    public function generate(string $html): string
    {
        if (is_null($binaryPath = $this->findTailwindBinary())) {
            throw TailwindBinaryNotFound::create();
        }

        $dir = TemporaryDirectory::make()->deleteWhenDestroyed();

        file_put_contents($htmlFile = $dir->path('content.html'), $html);

        $css = sprintf(
            "%s\n@source \"%s\";",
            $this->cssResolver->getContent(),
            $htmlFile
        );

        file_put_contents($input = $dir->path('input.css'), $css);

        $process = new Process([
            $binaryPath,
            '--input', $input,
            '--output', $output = $dir->path('output.css'),
        ]);

        $process->setTimeout(30)->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(
                'Tailwind CSS compilation failed: '.$process->getErrorOutput()
            );
        }

        return file_get_contents($output);
    }
}
