<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;

/**
 * Class RecursionFreeLifetimeResolver
 */
final class RecursionFreeLifetimeResolver implements LifetimeResolver
{
    /**
     * {@inheritDoc}
     */
    public function resolve(Definition $definition, UnmodifiableDefinitionMap $definitionMap): Lifetime
    {
        if ($definition->lifetime() !== null) {
            return $definition->lifetime();
        }

        return SingletonLifetime::get();
    }
}
