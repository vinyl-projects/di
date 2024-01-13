<?php

declare(strict_types=1);

namespace vinyl\di;

use Psr\Container\ContainerInterface;
use vinyl\di\definition\DefinitionTransformer;
use vinyl\di\definition\RecursiveDefinitionTransformer;
use vinyl\di\factory\argument\ArrayValue;
use vinyl\di\factory\argument\DefinitionFactoryValue;
use vinyl\di\factory\argument\EnumFactoryValue;
use vinyl\di\factory\FactoryValue;
use vinyl\std\lang\collections\Map;
use vinyl\std\lang\collections\MutableMap;
use function array_key_exists;
use function assert;
use function is_callable;
use function sprintf;
use function vinyl\std\lang\collections\mutableMapOf;

/**
 * Class DeveloperFactory
 *
 * Lazy factory
 */
final class DeveloperFactory implements ObjectFactory, ContainerAware
{
    private ?ContainerInterface $container = null;

    private LazyFactoryMetadataProvider $lazyFactoryMetadataProvider;

    /**
     * DeveloperFactory constructor.
     */
    public function __construct(LazyFactoryMetadataProvider $lazyFactoryMetadataProvider,)
    {
        $this->lazyFactoryMetadataProvider = $lazyFactoryMetadataProvider;
    }

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $arguments
     *
     * @throws \vinyl\di\NotEnoughArgumentsPassedException
     * @throws \vinyl\di\NotFoundException
     * @psalm-suppress InvalidStringClass
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedInferredReturnType
     */
    public function create(string $definitionId, ?array $arguments = null): object
    {
        assert($this->container !== null);

        $factoryMetadata = $this->lazyFactoryMetadataProvider->get($definitionId);

        $resolvedArguments = [];
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
                /** @var string $value */
                $resolvedArguments[] = $this->container->get($value);
                continue;
            }

            if ($valueObject instanceof ArrayValue) {
                /** @var array<string|int, FactoryValue> $value */
                $resolvedArguments[] = $this->resolveArrayArgument($value);
                continue;
            }

            if ($valueObject instanceof EnumFactoryValue) {
                assert(!is_null($valueObject->type()));
                $resolvedArguments[] = constant("{$valueObject->type()}::{$value}");
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
        return $this->lazyFactoryMetadataProvider->has($id);
    }

    /**
     * Recursive loop through array and instantiate objects if needed
     *
     * @param \vinyl\di\factory\FactoryValue[] $valueMetadataList
     *
     * @return array<int|string, mixed>
     * @throws \vinyl\di\NotFoundException
     * @psalm-suppress MixedAssignment
     * @psalm-suppress PossiblyNullReference
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

                assert(is_string($itemValue));

                $result[$key] = $this->container->get($itemValue);
                continue;
            }

            if ($item instanceof ArrayValue) {
                /** @var \vinyl\di\factory\FactoryValue[] $itemValue */
                $result[$key] = $this->resolveArrayArgument($itemValue);
                continue;
            }

            $result[$key] = $itemValue;
        }

        return $result;
    }
}
