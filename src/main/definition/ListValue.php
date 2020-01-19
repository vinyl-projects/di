<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\definition\value\Mergeable;
use vinyl\di\definition\value\Sortable;

/**
 * Interface ListValue
 */
interface ListValue extends DefinitionValue, Mergeable, Sortable
{
    /**
     * Appends the specified value to the end of this list
     */
    public function add(OrderableValue $value): void;

    /**
     * @return array<OrderableValue>
     */
    public function value():? array;
}
