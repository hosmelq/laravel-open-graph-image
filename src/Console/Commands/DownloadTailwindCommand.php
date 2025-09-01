<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Console\Commands;

use function Safe\chmod;
use function Safe\preg_match;
use function Safe\rename;
use function Safe\unlink;

use GuzzleHttp\Client;
use HosmelQ\OpenGraphImage\CSS\Concerns\FindsTailwindBinary;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\PcreException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process;
use Throwable;

#[AsCommand(name: 'open-graph-image:download-tailwind-binary')]
class DownloadTailwindCommand extends Command
{
    use FindsTailwindBinary;

    /**
     * @var string
     */
    protected $description = 'Download Tailwind CSS standalone CLI binary for Open Graph image generation';

    /**
     * @var string
     */
    protected $signature = 'open-graph-image:download-tailwind-binary';

    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        return (int) ! tap($this->installTailwind(), function (bool $installed): void {
            if ($installed) {
                $this->components->info('Tailwind CLI installed successfully.');
                $this->newLine();
            }
        });
    }

    /**
     * Install the Tailwind CLI binary if not present.
     *
     * @throws FileNotFoundException
     */
    public function installTailwind(): bool
    {
        $gitIgnorePath = base_path('.gitignore');

        if (File::exists($gitIgnorePath)) {
            $contents = File::get($gitIgnorePath);

            $filesToAppend = collect(['tailwindcss-cli', 'tailwindcss-cli.exe'])
                ->filter(fn (string $file): bool => ! str_contains($contents, $file.PHP_EOL))
                ->implode(PHP_EOL);

            if ($filesToAppend !== '') {
                File::append($gitIgnorePath, PHP_EOL.$filesToAppend.PHP_EOL);
            }
        }

        try {
            return $this->ensureTailwindBinaryMeetsRequirements(
                $this->ensureTailwindBinaryIsInstalled()
            );
        } catch (Throwable $throwable) {
            $this->components->error($throwable->getMessage());

            return false;
        }
    }

    /**
     * Download the Tailwind CSS binary from GitHub.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    private function downloadTailwindBinary(): void
    {
        $arch = php_uname('m');

        $assetName = match (true) {
            PHP_OS_FAMILY === 'Darwin' && $arch === 'arm64' => 'tailwindcss-macos-arm64',
            PHP_OS_FAMILY === 'Darwin' && $arch === 'x86_64' => 'tailwindcss-macos-x64',
            PHP_OS_FAMILY === 'Linux' && $arch === 'aarch64' => 'tailwindcss-linux-arm64',
            PHP_OS_FAMILY === 'Linux' && $arch === 'x86_64' => 'tailwindcss-linux-x64',
            PHP_OS_FAMILY === 'Windows' => 'tailwindcss-windows-x64.exe',
            default => null,
        };

        if (is_null($assetName)) {
            throw new RuntimeException('Your system architecture is not currently supported for automatic binary download. Please open an issue so we can check if there is a binary available for your system.');
        }

        $response = Http::accept('application/vnd.github+json')
            ->withHeaders(['X-GitHub-Api-Version' => '2022-11-28'])
            ->get('https://api.github.com/repos/tailwindlabs/tailwindcss/releases/latest')
            ->throw(fn () => $this->components->error('Failed to download Tailwind CLI.'));

        /** @var null|array{browser_download_url: string} $asset */
        $asset = $response->collect('assets')->firstWhere('name', $assetName);

        if (is_null($asset)) {
            throw new RuntimeException('Tailwind CLI asset not found.');
        }

        $path = base_path(PHP_OS_FAMILY === 'Windows' ? 'tailwindcss-cli.exe' : 'tailwindcss-cli');

        $this->components->task('Downloading Tailwind CLI binary', function () use ($asset, $path): void {
            File::ensureDirectoryExists(dirname($path));

            (new Client())->get($asset['browser_download_url'], [
                'sink' => $path,
            ]);

            if (PHP_OS_FAMILY !== 'Windows') {
                chmod($path, 0755);
            }
        });
    }

    /**
     * Ensure the Tailwind binary is installed.
     */
    private function ensureTailwindBinaryIsInstalled(): string
    {
        if (! is_null($tailwindBinary = $this->findTailwindBinary())) {
            return $tailwindBinary;
        }

        if ($this->confirm('Unable to locate Tailwind CLI binary. Should we download the binary for your operating system?', true)) {
            $this->downloadTailwindBinary();
        }

        return base_path(PHP_OS_FAMILY === 'Windows' ? 'tailwindcss-cli.exe' : 'tailwindcss-cli');
    }

    /**
     * Ensure the Tailwind binary meets version requirements.
     *
     * @throws PcreException
     * @throws FilesystemException
     */
    private function ensureTailwindBinaryMeetsRequirements(string $binaryPath): bool
    {
        $process = new Process([$binaryPath, '--help']);

        $process->run();

        if (preg_match('/(?:tailwindcss\s*)?v?(\d+\.\d+\.\d+)/i', $process->getOutput(), $matches) !== 1) {
            $platform = sprintf(
                'Platform: %s, Architecture: %s, PHP: %s',
                PHP_OS_FAMILY,
                php_uname('m'),
                PHP_VERSION
            );

            $this->components->warn(sprintf(
                'Unable to determine the current Tailwind CLI binary version. %s. Binary path: %s. Please report this issue with the platform details: https://github.com/hosmelq/laravel-open-graph-image/issues/new.',
                $platform,
                $binaryPath
            ));

            return false;
        }

        $version = $matches[1];

        if (version_compare($version, '4.0.0', '<')) {
            $this->components->warn('This package requires Tailwind CSS v4.0.0 or higher. Found version: '.$version);

            if ($this->confirm('Should we download the latest Tailwind CSS binary version for your operating system?', true)) {
                rename($binaryPath, $binaryPath.'.backup');

                try {
                    $this->downloadTailwindBinary();
                } catch (Throwable $e) {
                    report($e);

                    rename($binaryPath.'.backup', $binaryPath);

                    $this->components->warn('Unable to download Tailwind CSS binary. The underlying error has been logged.');

                    return false;
                }

                unlink($binaryPath.'.backup');
            }
        }

        return true;
    }
}
