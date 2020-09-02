<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\definition\value\Mergeable;
use vinyl\di\definition\value\Sortable;

/**
 * Interface ListValue
 */
interface ListValue extends DefinitionValue, Mergeable
{
    /**
     * Appends the specified value to the end of this list
     */
    public function add(DefinitionValue $value): void;

    /**
     * @return array<DefinitionValue>
     */
    public function value():? array;
}
