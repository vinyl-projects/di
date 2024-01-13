<?php

declare(strict_types=1);

namespace vinyl\di;

use OutOfBoundsException;
use function array_key_exists;

/**
 * Class LifetimeCodeMap
 */
final class UnmodifiableLifetimeCodeMap implements LifetimeCodeMap
{
    /** @var array<string, string> */
    private array $map;

    /**
     * LifetimeCodeMap constructor.
     *
     * @param array<string, string> $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $definitionId): string
    {
        if (array_key_exists($definitionId, $this->map)) {
            return $this->map[$definitionId];
        }

        throw new OutOfBoundsException("Lifetime code for given [{$definitionId}] id not found.");
    }

    /**
     * {@inheritDoc}
     */
    public function contains(string $definitionId): bool
    {
        return array_key_exists($definitionId, $this->map);
    }
}
