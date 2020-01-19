<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration;

use vinyl\di\CompiledFactory;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\factory\FactoryPerServiceCompiler;
use vinyl\di\ObjectFactory;
use function md5;
use function random_bytes;

/**
 * Class ServicePerFactoryContainerTest
 */
class ServicePerFactoryContainerTest extends AbstractContainerTest
{
    /**
     * {@inheritDoc}
     */
    protected function createFactory(FactoryMetadataMap $classFactoryMetadataMap): ObjectFactory
    {
        $factoryClassName = self::getNextClassName();

        $compiler = new FactoryPerServiceCompiler();
        $compiler->compile($factoryClassName, $classFactoryMetadataMap);

        return new CompiledFactory($factoryClassName);
    }

    private static function getNextClassName(): string
    {
        return 'Vinyl\\FactoryClass_' . md5(random_bytes(32));
    }
}
