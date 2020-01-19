<?php

declare(strict_types=1);

namespace vinyl\diTest\integration;

use vinyl\di\Container;
use vinyl\di\definition\ProxyValue;
use vinyl\di\definition\RecursionFreeClassResolver;
use vinyl\di\definition\RecursiveDefinitionTransformer;
use vinyl\di\definition\valueProcessor\ProxyValueProcessor;
use vinyl\di\definition\valueProcessor\ValueProcessorCompositor;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\DeveloperFactory;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\LifetimeProvider;
use vinyl\di\ObjectFactory;

/**
 * Class DeveloperContainerTest
 */
class DeveloperContainerTest extends AbstractContainerTest
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
        $classResolver = new RecursionFreeClassResolver();
        $valueProcessorComposite = new ValueProcessorCompositor([
            ProxyValue::class => new ProxyValueProcessor(),
        ],
            $classResolver
        );
        $typeMetadataBuilder = new RecursiveDefinitionTransformer(
            $valueProcessorComposite,
            $classResolver
        );
        $definitionMapBuilder = new DefinitionMapBuilder();
        $builderFunction($definitionMapBuilder);
        $definitionMap = $definitionMapBuilder->build();

        $lifetimeProvider = new LifetimeProvider($definitionMap->toLifetimeArrayMap());

        return new Container(
            $lifetimeProvider,
            new DeveloperFactory($definitionMap, $lifetimeProvider, $typeMetadataBuilder)
        );
    }
}
