<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use LogicException;
use SplStack;
use vinyl\di\AliasOnAliasDefinition;
use vinyl\di\Definition;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;
use function array_key_exists;
use function assert;

/**
 * Class RecursionFreeClassResolver
 */
final class RecursionFreeClassResolver implements ClassResolver
{
    /**
     * {@inheritDoc}
     */
    public function resolve(Definition $definition, Map $definitionMap): ClassObject
    {
        assert(self::assertGivenDefinitionAreSame($definition, $definitionMap));

        $stack = new SplStack();
        $stack->push($definition);

        $visitedDefinitions = [];
        while (!$stack->isEmpty()) {
            /** @var Definition $currentDefinition */
            $currentDefinition = $stack->pop();
            if (array_key_exists($currentDefinition->id(), $visitedDefinitions)) {
                throw DefinitionCircularReferenceFoundException::create($currentDefinition->id(), $visitedDefinitions);
            }

            $visitedDefinitions[$currentDefinition->id()] = true;

            if ($currentDefinition instanceof AliasOnAliasDefinition) {
                if (!$definitionMap->containsKey($currentDefinition->parentId())) {
                    throw new ClassResolverException("Unable to resolve class for {$definition->id()} definition id. Definition or one of parent refers to undeclared parent id [{$currentDefinition->parentId()}].");
                }

                $definitionFromMap = $definitionMap->get($currentDefinition->parentId());
                $stack->push($definitionFromMap);
                continue;
            }

            if (!$definitionMap->containsKey($currentDefinition->classObject()->name())) {
                return $currentDefinition->classObject();
            }

            $definitionFromMap = $definitionMap->get($currentDefinition->classObject()->name());

            if ($definitionFromMap === $currentDefinition) {
                return $currentDefinition->classObject();
            }

            $stack->push($definitionFromMap);
        }

        throw new LogicException("Unable to resolve class for {$definition->id()} definition id.");
    }

    /**
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     *
     * @return bool
     */
    private static function assertGivenDefinitionAreSame(Definition $definition, Map $definitionMap): bool
    {
        if (!$definitionMap->containsKey($definition->id())) {
            return true;
        }

        return $definitionMap->get($definition->id()) === $definition;
    }
}
