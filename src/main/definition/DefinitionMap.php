<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use ArrayIterator;
use Iterator;
use OutOfBoundsException;
use vinyl\di\Definition;
use function array_key_exists;
use function count;

/**
 * Class DefinitionMap
 */
final class DefinitionMap implements UnmodifiableDefinitionMap
{
    /** @var array<string, \vinyl\di\Definition> */
    private array $map;

    /**
     * DefinitionMap constructor.
     *
     * @param array<string, Definition> $definitionArrayMap indexed by definition id
     */
    public function __construct(array $definitionArrayMap)
    {
        $this->map = $definitionArrayMap;
    }

    /**
     * {@inheritDoc}
     */
    public function insert(Definition $dependency): void
    {
        $this->map[$dependency->id()] = $dependency;
    }

    /**
     * {@inheritDoc}
     */
    public function contains(string $definitionId): bool
    {
        return array_key_exists($definitionId, $this->map);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $definitionId): Definition
    {
        if (!array_key_exists($definitionId, $this->map)) {
            throw new OutOfBoundsException("Definition with given id[{$definitionId}] is not found.");
        }

        return $this->map[$definitionId];
    }

    /**
     * {@inheritDoc}
     */
    public function find(string $definitionId): ?Definition
    {
        return $this->map[$definitionId] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->map);
    }

    /**
     * {@inheritDoc}
     */
    public function toLifetimeArrayMap(): array
    {
        $lifetimeMap = [];
        /** @var Definition $definition */
        foreach ($this->map as $definitionId => $definition) {
            $lifetimeMap[$definitionId] = $definition->lifetime() !== null
                ? $definition->lifetime()->code()
                : SingletonLifetime::get()->code();
        }

        return $lifetimeMap;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->map);
    }
}
