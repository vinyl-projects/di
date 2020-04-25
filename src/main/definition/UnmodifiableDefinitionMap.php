<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use Countable;
use Iterator;
use IteratorAggregate;
use vinyl\di\Definition;

/**
 * Interface UnmodifiableDefinitionMap
 *
 * @extends \IteratorAggregate<string, \vinyl\di\Definition>
 */
interface UnmodifiableDefinitionMap extends IteratorAggregate, Countable
{
    /**
     * Returns true if this map contains definition for the specified key.
     */
    public function contains(string $definitionId): bool;

    /**
     * Returns the definition to which the specified key is mapped or throws an exception.
     *
     * @throws \OutOfBoundsException if definition with given key not found in map
     */
    public function get(string $definitionId): Definition;

    /**
     * Returns the definition to which the specified key is mapped or null
     */
    public function find(string $definitionId): ?Definition;

    /**
     * @return \Iterator<string, \vinyl\di\Definition>
     */
    public function getIterator(): Iterator;
}
