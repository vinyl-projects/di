<?php

declare(strict_types=1);

namespace vinyl\di\factory;

use vinyl\std\lang\ClassObject;

/**
 * Interface Compiler
 */
interface Compiler
{
    /**
     * Compiles factory metadata into native php factory
     *
     * @throws \vinyl\di\factory\CompilerException
     */
    public function compile(string $factoryClassName, FactoryMetadataMap $factoryMetadataMap): ClassObject;
}
