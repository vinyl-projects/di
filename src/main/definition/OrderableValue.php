<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface OrderableValue
 */
interface OrderableValue extends DefinitionValue, Orderable
{
    public const DEFAULT_SORT_ORDER = 0;
}
