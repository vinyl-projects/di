<?php

declare(strict_types=1);

namespace vinyl\di;

use Psr\Container\ContainerInterface;
use vinyl\di\definition\DefinitionMap;
use vinyl\di\definition\DefinitionTransformer;
use vinyl\di\definition\RecursiveDefinitionTransformer;
use vinyl\di\factory\argument\ArrayValue;
use vinyl\di\factory\argument\DefinitionFactoryValue;
use vinyl\di\factory\FactoryMetadataMap;
use function array_key_exists;
use function assert;
use function is_callable;
use function sprintf;

/**
 * Class DeveloperFactory
 *
 * Lazy factory
 */
final class DeveloperFactory implements ObjectFactory, ContainerAware
{
    private DefinitionMap $definitionMap;
    private ContainerInterface $container;
    private DefinitionTransformer $definitionTransformer;
    private FactoryMetadataMap $factoryMetadataMap;
    private LifetimeProvider $lifetimeMap;

    /**
     * RuntimeFactory constructor.
     */
    public function __construct(
        DefinitionMap $metadataCollection,
        LifetimeProvider $lifetimeMap,
        ?DefinitionTransformer $definitionTransformer = null
    ) {
        $this->definitionMap = $metadataCollection;
        $this->definitionTransformer = $definitionTransformer ?? new RecursiveDefinitionTransformer();
        $this->factoryMetadataMap = new FactoryMetadataMap();
        $this->lifetimeMap = $lifetimeMap;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $arguments
     *
     * @throws \vinyl\di\NotEnoughArgumentsPassedException
     * @throws \vinyl\di\NotFoundException
     */
    public function create(string $definitionId, ?array $arguments = null): object
    {
        if (!$this->definitionMap->contains(($definitionId)) && !$this->factoryMetadataMap->contains($definitionId)) {
            throw new NotFoundException("[{$definitionId}] not found.");
        }

        if (!$this->factoryMetadataMap->contains($definitionId)) {
            $factoryMetadataMap = $this->definitionTransformer->transform(
                $this->definitionMap->get($definitionId),
                $this->definitionMap
            );

            $this->factoryMetadataMap->add($factoryMetadataMap);
            $this->lifetimeMap->add(new LifetimeProvider($this->definitionMap->toLifetimeArrayMap()));
        }

        $factoryMetadata = $this->factoryMetadataMap->get($definitionId);

        $resolvedArguments = [];
        /** @var \vinyl\di\factory\FactoryValue $valueObject */
        foreach ($factoryMetadata->values as $name => $valueObject) {
            if ($arguments !== null && array_key_exists($name, $arguments)) {
                $resolvedArguments[] = $arguments[$name];
                continue;

            }

            if ($valueObject->isMissed()) {
                throw new NotEnoughArgumentsPassedException(
                    sprintf('type [%s] require [%s] arguments.', $factoryMetadata->class, $name)
                );
            }

            $value = $valueObject->value();

            if ($value === null) {
                $resolvedArguments[] = null;
                continue;
            }

            if ($valueObject instanceof DefinitionFactoryValue) {
                $resolvedArguments[] = $this->container->get($value);
                continue;
            }

            if ($valueObject instanceOf ArrayValue) {
                $resolvedArguments[] = $this->resolveArrayArgument($value);
                continue;
            }

            $resolvedArguments[] = $value;
        }

        $constructor = $factoryMetadata->constructor;

        if ($constructor !== null) {
            assert(is_callable($constructor));

            return $constructor(...$resolvedArguments);
        }

        return new $factoryMetadata->class(...$resolvedArguments);
    }

    /**
     * {@inheritDoc}
     */
    public function injectContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        return $this->definitionMap->contains($id);
    }

    /**
     * Recursive loop through array and instantiate objects if needed
     *
     * @param \vinyl\di\factory\FactoryValue[] $valueMetadataList
     *
     * @return array<int|string, mixed>
     * @throws \vinyl\di\NotFoundException
     */
    private function resolveArrayArgument(array $valueMetadataList): array
    {
        $result = [];
        foreach ($valueMetadataList as $key => $item) {

            $itemValue = $item->value();

            if ($itemValue === null) {
                $result[$key] = null;
                continue;
            }

            if ($item instanceof DefinitionFactoryValue) {

                $result[$key] = $this->container->get($itemValue);
                continue;
            }

            if ($item instanceOf ArrayValue) {
                /** @var \vinyl\di\factory\FactoryValue[] $itemValue */
                $result[$key] = $this->resolveArrayArgument($itemValue);
                continue;
            }

            $result[$key] = $itemValue;
        }

        return $result;
    }
}
