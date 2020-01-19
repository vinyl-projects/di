<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory;

use vinyl\di\Container;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\DeveloperFactory;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\LifetimeProvider;
use vinyl\di\ObjectFactory;

/**
 * Class DeveloperFactoryTest
 */
class DeveloperFactoryTest extends AbstractFactoryTest
{
    /**
     * {@inheritDoc}
     */
    public function createFactory(FactoryMetadataMap $classFactoryMetadataMap, array $lifetimeArrayMap): ObjectFactory
    {
        throw new \RuntimeException('Not implemented.');
    }

    protected function createContainer(?callable $builderFunction = null): ObjectFactory
    {
        $definitionMapBuilder = new DefinitionMapBuilder();
        if ($builderFunction !== null) {
            $builderFunction($definitionMapBuilder);
        }

        $definitionMap = $definitionMapBuilder->build();

        $lifetimeMap = new LifetimeProvider($definitionMap->toLifetimeArrayMap());
        $factory = new DeveloperFactory($definitionMap, $lifetimeMap);
        new Container($lifetimeMap, $factory);

        return $factory;
    }
}
