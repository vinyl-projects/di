<?php

declare(strict_types=1);

namespace vinyl\di\factory;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;
use function array_key_exists;
use function count;
use function sprintf;

/**
 * Class FactoryMetadataMap
 *
 * @implements IteratorAggregate<string, FactoryMetadata>
 */
final class FactoryMetadataMap implements IteratorAggregate, Countable
{
    /** @var array<string, FactoryMetadata> indexed by id */
    private array $items;

    /**
     * TypeMetadataCollection constructor.
     *
     * @param array<string, FactoryMetadata> $typeMetadataMap indexed by id
     */
    public function __construct(array $typeMetadataMap = [])
    {
        $this->items = $typeMetadataMap;
    }

    public function add(FactoryMetadataMap $classFactoryMetadataMap): void
    {
        /** @noinspection AdditionOperationOnArraysInspection */
        $this->items += $classFactoryMetadataMap->items;
    }

    public function put(FactoryMetadata $classFactoryMetadata): void
    {
        $this->items[$classFactoryMetadata->id] = $classFactoryMetadata;
    }

    public function contains(string $id): bool
    {
        return array_key_exists($id, $this->items);
    }

    /**
     */
    public function get(string $id): FactoryMetadata
    {
        if (!array_key_exists($id, $this->items)) {
            throw new OutOfBoundsException(sprintf('Factory metadata for [%s] not found.', $id));
        }

        return $this->items[$id];
    }

    /**
     * @return ArrayIterator<string, \vinyl\di\factory\FactoryMetadata>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->items);
    }
}
