<?php

declare(strict_types=1);

namespace vinyl\di;

/**
 * Interface AliasIdResolver
 *
 */
interface IdResolver
{
    /**
     * Resolves the given ID and returns the corresponding resolved ID.
     *
     * @param string $id The ID to resolve.
     *
     * @return string The resolved id value. If no Alias found, given ID returned
     */
    public function resolve(string $id): string;
}
