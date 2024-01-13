<?php

declare(strict_types=1);

namespace vinyl\di;

use OutOfBoundsException;

/**
 * Class LazyLifetimeCodeMap
 */
final readonly class LazyLifetimeCodeMap implements LifetimeCodeMap
{
    /**
     * LazyLifetimeCodeMap constructor.
     */
    public function __construct(private LazyFactoryMetadataProvider $lazyFactoryMetadataProvider)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $definitionId): string
    {
        if ($this->lazyFactoryMetadataProvider->has($definitionId)) {
            try {
                return $this->lazyFactoryMetadataProvider->get($definitionId)->lifetimeCode;
            } catch (NotFoundException $e) {
                throw new OutOfBoundsException("Lifetime code for given [{$definitionId}] id not found.");
            }
        }

        throw new OutOfBoundsException("Lifetime code for given [{$definitionId}] id not found.");
    }

    /**
     * {@inheritDoc}
     */
    public function contains(string $definitionId): bool
    {
        return $this->lazyFactoryMetadataProvider->has($definitionId);
    }
}
