<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory;

use vinyl\di\CompiledFactory;
use vinyl\di\Container;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\factory\FactoryPerServiceCompiler;
use vinyl\di\LifetimeProvider;
use vinyl\di\ObjectFactory;
use function md5;
use function random_bytes;

/**
 * Class CompiledFactoryPerServiceTest
 */
class CompiledFactoryPerServiceTest extends AbstractFactoryTest
{
    /**
     * {@inheritDoc}
     */
    public function createFactory(FactoryMetadataMap $classFactoryMetadataMap, array $lifetimeArrayMap): ObjectFactory
    {
        $factoryClass = self::getNextClassName();
        $compiler = new FactoryPerServiceCompiler();
        $compiler->compile($factoryClass, $classFactoryMetadataMap);
        $factory = new CompiledFactory($factoryClass);

        new Container(new LifetimeProvider($lifetimeArrayMap), $factory);

        return $factory;
    }

    private static function getNextClassName(): string
    {
        return 'vinyl\\diTestAsset\\FactoryClass_' . md5(random_bytes(32));
    }
}
