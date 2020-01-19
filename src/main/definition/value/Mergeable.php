<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

use vinyl\di\definition\DefinitionValue;

/**
 * Interface Mergeable
 *
 * Interface representing an object whose value set can be merged with
 * another {@see Mergeable} object.
 */
interface Mergeable
{
    /**
     * Merge current value with provided one
     *
     * @return \vinyl\di\definition\DefinitionValue New object with merged value
     * @throws \InvalidArgumentException if the supplied {@see Mergeable} object can't be merged with current
     */
    public function merge(Mergeable $mergeableValue): DefinitionValue;
}
