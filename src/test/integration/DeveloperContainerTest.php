<?php

declare(strict_types=1);

namespace vinyl\diTest\integration;

use vinyl\di\Container;
use vinyl\di\definition\ProxyValue;
use vinyl\di\definition\RecursionFreeClassResolver;
use vinyl\di\definition\RecursionFreeLifetimeResolver;
use vinyl\di\definition\RecursiveDefinitionTransformer;
use vinyl\di\definition\valueProcessor\ProxyValueProcessor;
use vinyl\di\definition\valueProcessor\ValueProcessorCompositor;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\DeveloperFactory;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\ModifiableLifetimeCodeMap;
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
        $lifetimeResolver = new RecursionFreeLifetimeResolver();
        $valueProcessorComposite = new ValueProcessorCompositor(
            [ProxyValue::class => new ProxyValueProcessor($lifetimeResolver)],
            $classResolver
        );
        $typeMetadataBuilder = new RecursiveDefinitionTransformer($valueProcessorComposite, $classResolver, $lifetimeResolver);
        $definitionMapBuilder = new DefinitionMapBuilder();
        $builderFunction($definitionMapBuilder);
        $definitionMap = $definitionMapBuilder->build();

        $lifetimeCodeMap = new ModifiableLifetimeCodeMap($definitionMap->toLifetimeArrayMap());

        return new Container(
            $lifetimeCodeMap,
            new DeveloperFactory($definitionMap, $lifetimeCodeMap, $typeMetadataBuilder)
        );
    }
}
