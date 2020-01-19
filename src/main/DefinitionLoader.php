<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\di\definition\DefinitionMap;

/**
 * Interface DefinitionLoader
 */
interface DefinitionLoader
{
    /**
     * Loads container configuration from provided source
     */
    public function load(string $source): DefinitionMap;
}
