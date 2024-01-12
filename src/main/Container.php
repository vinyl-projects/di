<?php

declare(strict_types=1);

namespace vinyl\di;

use Psr\Container\ContainerInterface;
use vinyl\di\definition\PrototypeLifetime;
use vinyl\di\definition\SingletonLifetime;

/**
 * Class Container
 */
final class Container implements ContainerInterface
{
    private static ?string $singletonLifetimeCode = null;
    /** @var array<string, bool> */
    private static array $allowedLifetimeMap = [];

    /** @var array<string, object> */
    private array $sharedInstances = [];

    /**
     * Container constructor.
     */
    public function __construct(private LifetimeCodeMap $lifetimeCodeMap, private ObjectFactory $objectFactory)
    {
        if ($this->objectFactory instanceof ContainerAware) {
            $this->objectFactory->injectContainer($this);
        }

        $this->insertSharedInstance(ObjectFactory::class, $objectFactory)
            ->insertSharedInstance(ContainerInterface::class, $this);

        if (self::$singletonLifetimeCode === null) {
            self::$singletonLifetimeCode = SingletonLifetime::get()->code();
            self::$allowedLifetimeMap[SingletonLifetime::get()->code()] = true;
            self::$allowedLifetimeMap[PrototypeLifetime::get()->code()] = true;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get($id): object
    {
        $object = $this->sharedInstances[$id] ?? null;

        if ($object !== null) {
            return $object;
        }

        $object = $this->objectFactory->create($id);
        $lifetime = $this->lifetimeCodeMap->get($id);

        if (!isset(self::$allowedLifetimeMap[$lifetime])) {
            throw new ContainerException(
                "The requested [{$id}] service have [{$lifetime}] lifetime and can't be instantiated within root container."
            );
        }

        if ($lifetime === self::$singletonLifetimeCode) {
            $this->sharedInstances[$id] = $object;
        }

        return $object;
    }

    /**
     * {@inheritDoc}
     */
    public function has($id): bool
    {
        return $this->objectFactory->has($id);
    }

    public function insertSharedInstance(string $id, object $object): self
    {
        $this->sharedInstances[$id] = $object;

        return $this;
    }

    /**
     * Returns Map which contains lifetime data for instances
     */
    public function lifetimeMap(): LifetimeCodeMap
    {
        return $this->lifetimeCodeMap;
    }
}
