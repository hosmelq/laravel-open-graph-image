<?php

declare(strict_types=1);

namespace HosmelQ\OpenGraphImage\Contracts;

interface ModificationTimeResolver
{
    /**
     * Get the modification time of the resource.
     */
    public function getModificationTime(): int;
}
