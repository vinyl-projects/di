<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\definition\value\Mergeable;

/**
 * Interface MapValue
 */
interface MapValue extends DefinitionValue, Mergeable
{
    /**
     * Returns value stored in current object
     *
     * @return null|array<int|string, DefinitionValue>
     */
    public function value(): ?array;

    /**
     * Puts value to map
     *
     * @param string|int $key
     */
    public function put($key, DefinitionValue $value): void;

    /**
     * Returns value by key or null if not available
     *
     * @param string|int $key
     */
    public function findByKey($key): ?DefinitionValue;
}
