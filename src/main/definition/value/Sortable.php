<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

use vinyl\di\definition\Orderable;

/**
 * Interface Sortable
 *
 * {@see DefinitionValue} which implements this interface could be sorted by some internal rules.
 * Usually this interface should be used together with the {@see Orderable} interface
 */
interface Sortable
{
    /**
     * Sort an elements
     */
    public function sort(): void;
}
