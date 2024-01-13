<?php

declare(strict_types=1);

namespace vinyl\di;

/**
 * Interface UnmodifiableLifetimeCodeMap
 */
interface LifetimeCodeMap
{
    /**
     * Returns lifetime code associated with given definition id
     *
     * @throws \OutOfBoundsException if there is no lifetime that associated with given definition id
     */
    public function get(string $definitionId): string;

    /**
     * Checks weather this map contain lifetime for given definition
     */
    public function contains(string $definitionId): bool;
}
