<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use LogicException;
use SplStack;
use vinyl\di\AliasOnAliasDefinition;
use vinyl\di\Definition;
use vinyl\std\lang\collections\Map;

/**
 * Class RecursionFreeInstantiatorResolver
 */
final class RecursionFreeInstantiatorResolver implements InstantiatorResolver
{
    private ClassResolver $classResolver;

    /**
     * RecursionFreeInstantiatorResolver constructor.
     */
    public function __construct(ClassResolver $classResolver)
    {
        $this->classResolver = $classResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Definition $definition, Map $definitionMap): Instantiator
    {
        if ($definition->instantiator() !== null) {
            return $definition->instantiator();
        }

        try {
            $class = $this->classResolver->resolve($definition, $definitionMap);
        } catch (ClassResolverException $e) {
            throw new LogicException($e->getMessage(), 0, $e);
        }

        $stack = new SplStack();
        $stack->push($definition);

        while (!$stack->isEmpty()) {
            /** @var \vinyl\di\Definition $currentDefinition */
            $currentDefinition = $stack->pop();

            if ($currentDefinition->instantiator() !== null) {
                return $currentDefinition->instantiator();
            }

            if ($currentDefinition instanceof AliasOnAliasDefinition) {
                $parentDefinition = $definitionMap->get($currentDefinition->parentId());
                if ($parentDefinition->instantiator() !== null) {
                    return $parentDefinition->instantiator();
                }

                $stack->push($parentDefinition);
                continue;
            }

            if (!$definitionMap->containsKey($currentDefinition->classObject()->name())) {
                return new ConstructorInstantiator($class);
            }

            $parentDefinition = $definitionMap->get($currentDefinition->classObject()->name());

            if ($currentDefinition === $parentDefinition) {
                return new ConstructorInstantiator($class);
            }

            $stack->push($parentDefinition);
        }

        throw new LogicException("Unable to resolve instantiator for [{$definition->id()}]");
    }
}
