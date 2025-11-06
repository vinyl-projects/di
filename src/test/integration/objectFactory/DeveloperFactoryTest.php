<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory;

use Closure;
use vinyl\di\ContainerBuilder;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\DeveloperFactory;
use vinyl\di\ObjectFactory;

/**
 * Class DeveloperFactoryTest
 */
class DeveloperFactoryTest extends AbstractFactoryTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function createFactory(?Closure $builderFunction = null): ObjectFactory
    {
        $definitionMapBuilder = new DefinitionMapBuilder();
        if ($builderFunction !== null) {
            $builderFunction($definitionMapBuilder);
        }
        $definitionMap = $definitionMapBuilder->build();

        $container  = ContainerBuilder::create($definitionMap)
            ->useEvalMaterializerStrategy()
            ->useDeveloperFactory()
            ->build();

        $factory = $container->get(\vinyl\di\ObjectFactory::class);
        if (!$factory instanceof DeveloperFactory) {
            throw new \RuntimeException('Factory is not instance of DeveloperFactory');
        }

        return $factory;
    }
}
