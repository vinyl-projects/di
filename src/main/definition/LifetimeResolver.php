<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;

/**
 * Interface LifetimeResolver
 */
interface LifetimeResolver
{
    /**
     * Returns resolved {@see \vinyl\di\definition\Lifetime} for given definition
     */
    public function resolve(Definition $definition, UnmodifiableDefinitionMap $definitionMap): Lifetime;
}
