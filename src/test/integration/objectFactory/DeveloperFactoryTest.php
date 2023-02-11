<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory;

use vinyl\di\Container;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\DeveloperFactory;
use vinyl\di\ModifiableLifetimeCodeMap;
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

        $lifetimeMap = new ModifiableLifetimeCodeMap([]);
        $factory = new DeveloperFactory($definitionMap, $lifetimeMap);
        new Container($lifetimeMap, $factory);

        return $factory;
    }
}
