<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\scoped;

use vinyl\di\Container;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\DeveloperFactory;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\LifetimeProvider;
use vinyl\di\ObjectFactory;

/**
 * Class DeveloperContainerTest
 */
class DeveloperContainerTest extends AbstractScopedContainerTest
{
    /**
     * {@inheritDoc}
     */
    protected function createFactory(FactoryMetadataMap $classFactoryMetadataMap): ObjectFactory
    {
        throw new \RuntimeException('Not implemented.');
    }

    protected function createContainer(callable $builderFunction): Container
    {
        $definitionMapBuilder = new DefinitionMapBuilder();
        $builderFunction($definitionMapBuilder);
        $definitionMap = $definitionMapBuilder->build();

        $lifetimeMap = new LifetimeProvider($definitionMap->toLifetimeArrayMap());

        return new Container(
            $lifetimeMap,
            new DeveloperFactory($definitionMap, $lifetimeMap)
        );
    }
}
