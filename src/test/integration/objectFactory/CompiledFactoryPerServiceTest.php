<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory;

use vinyl\di\CompiledFactory;
use vinyl\di\Container;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\factory\DefinitionMapTransformer;
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
    protected function createFactory(?callable $builderFunction = null): ObjectFactory
    {
        $metadataBuilder = new DefinitionMapTransformer();
        $definitionMapBuilder = new DefinitionMapBuilder();
        if ($builderFunction !== null) {
            $builderFunction($definitionMapBuilder);
        }

        $definitionMap = $definitionMapBuilder->build();

        $factoryClassName = self::getNextClassName();
        $compiler = new FactoryPerServiceCompiler();
        $factoryClass = $compiler->compile($factoryClassName, $metadataBuilder->transform($definitionMap));
        $factory = new CompiledFactory($factoryClass);

        new Container(new UnmodifiableLifetimeCodeMap([]), $factory);

        return $factory;
    }

    private static function getNextClassName(): string
    {
        return 'vinyl\\diTestAsset\\FactoryClass_' . md5(random_bytes(32));
    }
}
