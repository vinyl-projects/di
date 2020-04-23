<?php

declare(strict_types=1);

namespace vinyl\di;

use ArrayIterator;
use OutOfBoundsException;
use function array_key_exists;
use function count;

/**
 * Class ModifiableLifetimeCodeMap
 */
class ModifiableLifetimeCodeMap implements LifetimeCodeMap
{
    /** @var array<string, string> */
    private array $map;

    /**
     * ModifiableLifetimeCodeMap constructor.
     *
     * @param array<string, string> $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @return \Iterator<string, string>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->map);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->map);
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

    /**
     * Inserts lifetime code to map
     */
    public function insert(string $definitionId, string $lifetimeCode): void
    {
        $this->map[$definitionId] = $lifetimeCode;
    }
}
