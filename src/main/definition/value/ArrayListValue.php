<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

use InvalidArgumentException;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\ListValue;
use vinyl\di\definition\OrderableValue;
use function count;
use function get_class;
use function sprintf;
use function usort;

/**
 * Class ArrayListValue
 */
final class ArrayListValue implements ListValue
{
    /** @var OrderableValue[] */
    private ?array $items;

    /**
     * ListValue constructor.
     *
     * @param OrderableValue[] $items
     */
    public function __construct(?array $items = null)
    {
        $this->items = $items;
    }

    /**
     * {@inheritDoc}
     */
    public function add(OrderableValue $value): void
    {
        $this->items[] = $value;
    }

    /**
     * {@inheritDoc}
     *
     * @return OrderableValue[]
     */
    public function value():? array
    {
        return $this->items;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(Mergeable $mergeableValue): DefinitionValue
    {
        if (!$mergeableValue instanceof ListValue) {
            #todo maybe it would be better to return $mergeableValue instead
            throw new InvalidArgumentException(
                sprintf('Cannot merge with object of type [%s]',
                get_class($mergeableValue))
            );
        }

        if ($this->items === null || count($this->items) === 0) {
            return clone $mergeableValue;
        }

        $items = [];
        $value = $mergeableValue->value();
        if ($value === null) {
            return clone $this;
        }

        foreach ($this->items as $item) {
            $items[] = clone $item;
        }

        /** @var OrderableValue $item */
        foreach ($value as $item) {
            $items[] = clone $item;
        }

        return new self($items);
    }

    public function __clone()
    {
        if ($this->items === null) {
            return;
        }

        $itemList = [];

        foreach ($this->items as $item) {
            $itemList[] = clone $item;
        }

        $this->items = $itemList;
    }

    /**
     * {@inheritDoc}
     */
    public function sort(): void
    {
        if ($this->items === null) {
            return;
        }

        usort($this->items, static fn(OrderableValue $a, OrderableValue $b): int => $a->order() <=> $b->order());
    }
}
