<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use vinyl\di\definition\value\Mergeable;
use function array_key_exists;
use function count;

/**
 * Class ValueMap
 *
 * @implements IteratorAggregate<string, \vinyl\di\definition\DefinitionValue>
 */
final class ValueMap implements IteratorAggregate, Countable
{
    /** @var array<string, \vinyl\di\definition\DefinitionValue> indexed by argument name */
    private array $values;

    /**
     * ValueMap constructor.
     *
     * @param array<string, \vinyl\di\definition\DefinitionValue> $values indexed by argument name
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * @returns \vinyl\di\definition\DefinitionValue|null previous value or null
     */
    public function put(string $argumentName, DefinitionValue $valueHolder): ?DefinitionValue
    {
        if ($argumentName === '') {
            throw new InvalidArgumentException('Argument name could not be empty.');
        }

        $result = $this->values[$argumentName] ?? null;
        $this->values[$argumentName] = $valueHolder;

        return $result;
    }

    public function find(string $argumentName): ?DefinitionValue
    {
        return $this->values[$argumentName] ?? null;
    }

    public function remove(string $argumentName): void
    {
        if (array_key_exists($argumentName, $this->values)) {
            unset($this->values[$argumentName]);
        }
    }

    /**
     * Merge current {@see ValueMap } with one or more {@see ValueMap }
     *
     * If several value maps are passed, they will be processed in order, the later map overwriting the previous.
     *
     * If {@see FactoryValue } implements {@see Mergeable} interface it will be merged with value from other map
     */
    public function merge(ValueMap ...$valueMapList): ValueMap
    {
        /** @var array<string, \vinyl\di\definition\DefinitionValue> $result */
        $result = [];

        /** @var string $argumentName */
        foreach ($this->values as $argumentName => $value) {
            /** @var \vinyl\di\definition\DefinitionValue $value */
            $result[$argumentName] = clone $value;
        }

        foreach ($valueMapList as $values) {
            /** @var string $argumentName */
            foreach ($values as $argumentName => $value) {
                if (!array_key_exists($argumentName, $result)) {
                    $result[$argumentName] = clone $value;
                    continue;
                }

                /** @var \vinyl\di\definition\DefinitionValue $currentValue */
                $currentValue = $result[$argumentName];
                if ($value instanceof Mergeable && $currentValue instanceof Mergeable) {
                    $result[$argumentName] = $currentValue->merge($value);
                    continue;
                }

                $result[$argumentName] = clone $value;
            }
        }

        return new ValueMap($result);
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return ArrayIterator<string, \vinyl\di\definition\DefinitionValue>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->values);
    }

    public function __clone()
    {
        $newValues = [];

        foreach ($this->values as $argumentName => $value) {
            $newValues[$argumentName] = clone $value;
        }

        $this->values = $newValues;
    }
}
