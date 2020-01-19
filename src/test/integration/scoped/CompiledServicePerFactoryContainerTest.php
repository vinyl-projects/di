<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration\scoped;

use vinyl\di\CompiledFactory;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\factory\FactoryPerServiceCompiler;
use vinyl\di\ObjectFactory;
use function md5;
use function random_bytes;

/**
 * Class CompiledServicePerFactoryContainerTest
 */
class CompiledServicePerFactoryContainerTest extends AbstractScopedContainerTest
{
    /**
     * {@inheritDoc}
     */
    protected function createFactory(FactoryMetadataMap $classFactoryMetadataMap): ObjectFactory
    {
        $factoryClass = self::getNextClassName();
        $compiler = new FactoryPerServiceCompiler();
        $compiler->compile($factoryClass, $classFactoryMetadataMap);

        return new CompiledFactory($factoryClass);
    }

    private static function getNextClassName(): string
    {
        return 'Vinyl\\FactoryClass_' . md5(random_bytes(32));
    }
}
