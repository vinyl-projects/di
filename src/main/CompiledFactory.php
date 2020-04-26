<?php

declare(strict_types=1);

namespace vinyl\di;

use Psr\Container\ContainerInterface;
use vinyl\std\lang\ClassObject;
use function assert;

/**
 * Class CompiledFactory
 */
final class CompiledFactory implements ObjectFactory, ContainerAware
{
    /** @var \vinyl\di\ObjectFactory&\vinyl\di\ContainerAware */
    private $factory;

    /**
     * Compiled constructor.
     */
    public function __construct(ClassObject $factoryClass)
    {
        $factory = $factoryClass->toReflectionClass()->newInstanceWithoutConstructor();
        assert($factory instanceof ObjectFactory && $factory instanceof ContainerAware);

        $this->factory = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $definitionId, ?array $arguments = null): object
    {
        return $this->factory->create($definitionId, $arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        return $this->factory->has($id);
    }

    /**
     * {@inheritDoc}
     */
    public function injectContainer(ContainerInterface $container): void
    {
        $this->factory->injectContainer($container);
    }

    public function __clone()
    {
        $this->factory = clone $this->factory;
    }
}
