<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory;

use vinyl\di\CompiledFactory;
use vinyl\di\Container;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\factory\FactoryPerServiceCompiler;
use vinyl\di\ObjectFactory;
use vinyl\di\UnmodifiableLifetimeCodeMap;
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
        $factoryClassName = self::getNextClassName();
        $compiler = new FactoryPerServiceCompiler();
        $factoryClass = $compiler->compile($factoryClassName, $classFactoryMetadataMap);
        $factory = new CompiledFactory($factoryClass);

        new Container(new UnmodifiableLifetimeCodeMap($lifetimeArrayMap), $factory);

        return $factory;
    }

    private static function getNextClassName(): string
    {
        return 'vinyl\\diTestAsset\\FactoryClass_' . md5(random_bytes(32));
    }
}
