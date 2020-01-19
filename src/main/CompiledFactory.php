<?php

declare(strict_types=1);

namespace vinyl\di;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use function class_exists;

/**
 * Class CompiledFactory
 */
final class CompiledFactory implements ObjectFactory, ContainerAware
{
    /** @var \vinyl\di\ObjectFactory&\vinyl\di\ContainerAware */
    private $factory;

    /**
     * Compiled constructor.
     *
     * @param string $factoryClass factory class name
     */
    public function __construct(string $factoryClass)
    {
        if (!class_exists($factoryClass)) {
            throw new InvalidArgumentException("Factory class [$factoryClass] not exists.");
        }

        $this->factory = new $factoryClass();
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
