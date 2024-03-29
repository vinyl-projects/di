<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use SplStack;
use vinyl\di\AliasOnAliasDefinition;
use vinyl\di\Definition;
use vinyl\std\lang\collections\Map;

/**
 * Class RecursionFreeLifetimeResolver
 */
final class RecursionFreeLifetimeResolver implements LifetimeResolver
{
    /**
     * {@inheritDoc}
     */
    public function resolve(Definition $definition, Map $definitionMap): Lifetime
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

            if (!$definitionMap->containsKey($currentDefinition->classObject()->name())) {
                break;
            }

            $parentDefinition = $definitionMap->get($currentDefinition->classObject()->name());

            if ($currentDefinition === $parentDefinition) {
                break;
            }

            $stack->push($parentDefinition);
        }

        if ($currentDefinition === null || $currentDefinition->lifetime() === null) {
            return SingletonLifetime::get();
        }

        throw new \LogicException("Unable to resolve lifetime for '{$definition->id()}' definition.");
    }
}
