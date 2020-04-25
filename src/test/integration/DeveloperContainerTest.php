<?php

declare(strict_types=1);

namespace vinyl\diTest\integration;

use Psr\Container\ContainerInterface;
use vinyl\di\ContainerBuilder;
use vinyl\di\DefinitionMapBuilder;

/**
 * Class DeveloperContainerTest
 */
final class DeveloperContainerTest extends AbstractContainerTest
{
    /**
     * {@inheritDoc}
     */
    protected function createContainer(callable $builderFunction): ContainerInterface
    {
        $definitionMapBuilder = new DefinitionMapBuilder();
        $builderFunction($definitionMapBuilder);
        $definitionMap = $definitionMapBuilder->build();

        return ContainerBuilder::create($definitionMap)
            ->useEvalMaterializerStrategy()
            ->useDeveloperFactory()
            ->build();
    }
}
