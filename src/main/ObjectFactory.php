<?php

declare(strict_types=1);

namespace vinyl\di;

/**
 * Interface ObjectFactory
 */
interface ObjectFactory
{
    /**
     * Create instance by id
     *
     * @param array<string, mixed> $arguments indexed by argument name
     */
    public function create(string $definitionId, ?array $arguments = null): object;

    /**
     * Checks whether requested type can be instantiated via factory
     */
    public function has(string $id): bool;
}
