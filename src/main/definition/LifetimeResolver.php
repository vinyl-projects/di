<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;
use vinyl\std\lang\collections\Map;

/**
 * Interface LifetimeResolver
 */
interface LifetimeResolver
{
    /**
     * Returns resolved {@see \vinyl\di\definition\Lifetime} for given definition
     *
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     */
    public function resolve(Definition $definition, Map $definitionMap): Lifetime;
}
