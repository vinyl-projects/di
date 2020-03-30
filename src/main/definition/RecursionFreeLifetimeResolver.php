<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use SplStack;
use vinyl\di\AliasOnAliasDefinition;
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

        $stack = new SplStack();
        $stack->push($definition);
        $currentDefinition = null;

        while (!$stack->isEmpty()) {
            /** @var \vinyl\di\Definition $currentDefinition */
            $currentDefinition = $stack->pop();

            if ($currentDefinition->lifetime() !== null) {
                return $currentDefinition->lifetime();
            }

            if ($currentDefinition instanceof AliasOnAliasDefinition) {
                $parentDefinition = $definitionMap->get($currentDefinition->parentId());
                if ($parentDefinition->lifetime() !== null) {
                    return $parentDefinition->lifetime();
                }

                $stack->push($parentDefinition);
                continue;
            }

            if (!$definitionMap->contains($currentDefinition->classObject()->className())) {
                break;
            }

            $parentDefinition = $definitionMap->get($currentDefinition->classObject()->className());

            if ($currentDefinition === $parentDefinition) {
                break;
            }

            $stack->push($parentDefinition);
        }

        if ($currentDefinition === null || $currentDefinition->lifetime() === null) {
            return SingletonLifetime::get();
        }

        return $currentDefinition->lifetime();
    }
}
