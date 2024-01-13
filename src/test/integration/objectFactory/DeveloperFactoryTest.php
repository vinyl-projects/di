<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory;

use vinyl\di\Container;
use vinyl\di\definition\RecursiveDefinitionTransformer;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\DeveloperFactory;
use vinyl\di\factory\DefinitionMapTransformer;
use vinyl\di\LazyFactoryMetadataProvider;
use vinyl\di\LazyLifetimeCodeMap;
use vinyl\di\ObjectFactory;

/**
 * Class DeveloperFactoryTest
 */
class DeveloperFactoryTest extends AbstractFactoryTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function createFactory(?callable $builderFunction = null): ObjectFactory
    {
        $definitionMapBuilder = new DefinitionMapBuilder();
        if ($builderFunction !== null) {
            $builderFunction($definitionMapBuilder);
        }

        $definitionMap = $definitionMapBuilder->build();

        $lazyFactoryMetadataProvider = new LazyFactoryMetadataProvider($definitionMap, new RecursiveDefinitionTransformer());
        $lifetimeMap = new LazyLifetimeCodeMap($lazyFactoryMetadataProvider);
        $factory = new DeveloperFactory($lazyFactoryMetadataProvider);
        new Container($lifetimeMap, $factory);

        return $factory;
    }
}
