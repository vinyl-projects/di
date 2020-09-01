<?php

declare(strict_types=1);

namespace vinyl\di\factory;

use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;

/**
 * Interface Compiler
 */
interface Compiler
{
    /**
     * Compiles factory metadata into native php factory
     *
     * @psalm-param Map<string, \vinyl\di\factory\FactoryMetadata> $factoryMetadataMap
     *
     * @throws \vinyl\di\factory\CompilerException
     */
    public function compile(string $factoryClassName, Map $factoryMetadataMap): ClassObject;
}
