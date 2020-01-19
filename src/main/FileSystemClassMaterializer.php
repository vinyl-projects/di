<?php

declare(strict_types=1);

namespace vinyl\di;

/**
 * Class FileSystemClassMaterializer
 */
final class FileSystemClassMaterializer implements ClassMaterializer
{
    /**
     * {@inheritDoc}
     */
    public function materialize(string $className, string $classContent): void
    {
        throw new \RuntimeException('Not implemented.');
    }
}
