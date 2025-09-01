<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Templates;

use function Safe\filemtime;

use HosmelQ\OpenGraphImage\Exceptions\TemplateNotFound;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Factory as ViewFactory;
use Safe\Exceptions\FilesystemException;

class TemplateModificationTimeResolver
{
    /**
     * Create a new template modification time resolver instance.
     */
    public function __construct(protected ViewFactory $viewFactory)
    {
    }

    /**
     * Get the modification time of a template.
     *
     * @throws TemplateNotFound
     * @throws FilesystemException
     */
    public function getModificationTime(string $template): int
    {
        $templatePath = $this->resolveTemplatePath($template);
        $engine = $this->viewFactory->getEngineFromPath($templatePath);

        if (! $engine instanceof CompilerEngine) {
            return filemtime($templatePath);
        }

        return $this->getCompiledTemplateModificationTime($templatePath, $engine);
    }

    /**
     * Get the modification time of a compiled template.
     *
     * @throws FilesystemException
     */
    protected function getCompiledTemplateModificationTime(string $templatePath, CompilerEngine $engine): int
    {
        $compiler = $engine->getCompiler();

        if ($compiler->isExpired($templatePath)) {
            $compiler->compile($templatePath);
        }

        return filemtime($compiler->getCompiledPath($templatePath));
    }

    /**
     * Resolve a template name to its file path.
     *
     * @throws TemplateNotFound
     */
    protected function resolveTemplatePath(string $template): string
    {
        $view = 'open-graph-image.templates.'.$template;

        if (! $this->viewFactory->exists($view)) {
            throw TemplateNotFound::create($template);
        }

        return $this->viewFactory->getFinder()->find($view);
    }
}
