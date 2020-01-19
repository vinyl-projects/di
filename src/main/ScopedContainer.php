<?php

declare(strict_types=1);

namespace vinyl\di;

use Psr\Container\ContainerInterface;
use vinyl\di\definition\ScopedLifetime;

/**
 * Class ScopedContainer
 */
final class ScopedContainer implements ContainerInterface
{
    private static ?string $scopedLifetime = null;
    private ContainerInterface $rootContainer;
    private ObjectFactory $objectFactory;
    private LifetimeProvider $serviceLifetimeStorage;

    /** @var array<string, object> */
    private array $scopedInstanceMap = [];

    /**
     * ScopedDI constructor.
     *
     * @param \vinyl\di\LifetimeProvider        $serviceLifetimeStorage
     * @param \Psr\Container\ContainerInterface $rootContainer
     * @param \vinyl\di\ObjectFactory           $objectFactory
     */
    public function __construct(
        LifetimeProvider $serviceLifetimeStorage,
        ContainerInterface $rootContainer,
        ObjectFactory $objectFactory
    ) {
        $this->rootContainer = $rootContainer;
        $this->objectFactory = $objectFactory;

        if ($this->objectFactory instanceof ContainerAware) {
            $this->objectFactory->injectContainer($this);
        }

        $this->serviceLifetimeStorage = $serviceLifetimeStorage;
        $this->scopedInstanceMap[ObjectFactory::class] = $this->objectFactory;
        $this->scopedInstanceMap[ContainerInterface::class] = $this;
        if (self::$scopedLifetime === null) {
            self::$scopedLifetime = ScopedLifetime::get()->code();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get($id): object
    {
        $object = $this->scopedInstanceMap[$id] ?? null;

        if ($object !== null) {
            return $object;
        }

        if ($this->serviceLifetimeStorage->get($id) !== self::$scopedLifetime) {
            return $this->rootContainer->get($id);
        }

        $object = $this->objectFactory->create($id);
        $this->scopedInstanceMap[$id] = $object;

        return $object;
    }

    /**
     * {@inheritDoc}
     */
    public function has($id): bool
    {
        return $this->objectFactory->has($id);
    }
}
