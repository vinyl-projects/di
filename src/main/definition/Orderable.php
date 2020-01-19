<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Interface Orderable
 *
 * Is an interface that can be implemented by objects that
 * should be orderable.
 *
 * The {@see Ordered::order() } order value can be interpreted as prioritization,
 * with the first object (with the lowest order value) having the highest
 * priority.
 *
 * @todo move to sdt lib
 */
interface Orderable
{
    public const HIGHEST_PRECEDENCE = PHP_INT_MIN;
    public const LOWEST_PRECEDENCE  = PHP_INT_MAX;

    /**
     * Returns order
     *
     * @return int
     */
    public function order(): int;
}
