<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\definition\value\Mergeable;
use vinyl\di\definition\value\Sortable;

/**
 * Interface MapValue
 */
interface MapValue extends DefinitionValue, Mergeable, Sortable
{
    /**
     * Returns value stored in current object
     *
     * @return null|array<int|string, OrderableValue>
     */
    public function value(): ?array;

    /**
     * Puts value to map
     *
     * @param string|int                          $key
     * @param \vinyl\di\definition\OrderableValue $value
     */
    public function put($key, OrderableValue $value): void;

    /**
     * Returns value by key or null if not available
     *
     * @param string|int $key
     *
     * @return \vinyl\di\definition\OrderableValue|null
     */
    public function findByKey($key): ?OrderableValue;
}
