<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

use InvalidArgumentException;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\MapValue;
use function array_key_exists;
use function count;
use function get_class;
use function sprintf;

/**
 * Class ArrayMapValue
 */
final class ArrayMapValue implements MapValue
{
    /** @var array<string|int, DefinitionValue>|null */
    private ?array $items;

    /**
     * MapValueHolder constructor.
     *
     * @param array<string|int, DefinitionValue>|null $items
     */
    public function __construct(?array $items = null)
    {
        $this->items = $items;
    }

    /**
     * @return array<string|int, DefinitionValue>|null
     */
    public function value(): ?array
    {
        return $this->items;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(Mergeable $mergeableValue): DefinitionValue
    {
        if (!$mergeableValue instanceof MapValue) {//assert here
            throw new InvalidArgumentException(
                sprintf('Cannot merge with object of type [%s]', get_class($mergeableValue))
            );
        }

        $newItems = [];

        if ($this->items === null || count($this->items) === 0) {
            return clone $mergeableValue;
        }

        $values = $mergeableValue->value();
        if ($values === null || count($values) === 0) {
            return clone $this;
        }

        /** @var DefinitionValue $item */
        foreach ($this->items as $key => $item) {
            $newItems[$key] = clone $item;
        }

        foreach ($values as $key => $item) {
            if (!array_key_exists($key, $newItems)) {
                $newItems[$key] = clone $item;
                continue;
            }

            /** @var DefinitionValue $currentItem */
            $currentItem = $newItems[$key];
            if ($currentItem instanceof Mergeable && $item instanceof Mergeable) {
                /** @var DefinitionValue $newItem */
                $newItem = $currentItem->merge($item);
                $newItems[$key] = $newItem;
                continue;
            }

            $newItems[$key] = clone $item;
        }

        return new ArrayMapValue($newItems);
    }

    public function __clone()
    {
        if ($this->items === null) {
            return;
        }

        $items = [];

        foreach ($this->items as $key => $item) {
            $items[$key] = clone $item;
        }

        $this->items = $items;
    }

    /**
     * {@inheritDoc}
     */
    public function put($key, DefinitionValue $value): void
    {
        //todo instead of void maybe it will be good to return previous value if available
        $this->items[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function findByKey($key): ?DefinitionValue
    {
        return $this->items[$key] ?? null;
    }
}
