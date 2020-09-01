<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use SplStack;
use vinyl\di\AliasOnAliasDefinition;
use vinyl\di\Definition;
use vinyl\di\definition\value\Mergeable;
use vinyl\std\lang\collections\Map;
use function array_reverse;
use function count;

/**
 * Class RecursionFreeValueCollector
 */
final class RecursionFreeValueCollector implements ValueCollector
{
    private ParentClassesProvider $parentClassesProvider;

    /**
     * RecursionFreeValueCollector constructor.
     */
    public function __construct(?ParentClassesProvider $parentClassesProvider = null)
    {
        $this->parentClassesProvider = $parentClassesProvider ?? new ParentClassesProvider();
    }

    /**
     * {@inheritDoc}
     */
    public function collect(Definition $definition, Map $definitionMap): ValueMap
    {
        /** @var ValueMap[] $valueMapList */
        $valueMapList = [];
        $stack = new SplStack();
        $stack->push($definition);

        while (!$stack->isEmpty()) {
            /** @var \vinyl\di\Definition $currentDefinition */
            $currentDefinition = $stack->pop();

            $valueMapList[] = $currentDefinition->argumentValues();

            if (!$currentDefinition->isArgumentInheritanceEnabled()) {
                break;
            }

            if ($currentDefinition instanceof AliasOnAliasDefinition) {
                if (!$definitionMap->containsKey($currentDefinition->parentId())) {
                    break;
                }

                $stack->push($definitionMap->get($currentDefinition->parentId()));
                continue;
            }

            $parentClasses = $this->parentClassesProvider->find($currentDefinition->classObject());

            foreach ($parentClasses as $parentClass) {
                $parentClassName = $parentClass->name();
                if (!$definitionMap->containsKey($parentClassName)) {
                    break;
                }

                $parentDefinition = $definitionMap->get($parentClassName);

                #could happen for definition with replaced class
                if ($parentDefinition === $currentDefinition) {
                    continue;
                }

                $valueMapList[] = $parentDefinition->argumentValues();

                if (!$parentDefinition->isArgumentInheritanceEnabled()) {
                    break;
                }
            }

            $currentDefinitionClassName = $currentDefinition->classObject()->name();
            if ($currentDefinition->id() !== $currentDefinitionClassName
                && $definitionMap->containsKey($currentDefinitionClassName)) {
                $stack->push($definitionMap->get($currentDefinitionClassName));
                continue;
            }
        }

        if (count($valueMapList) === 0) {
            return new ValueMap();
        }

        return self::merge(...array_reverse($valueMapList));
    }

    /**
     * Merge given {@see ValueMap } with one or more {@see ValueMap }
     *
     * If several value maps are passed, they will be processed in order, the later map overwriting the previous.
     *
     * If {@see FactoryValue } implements {@see Mergeable} interface it will be merged with value from other map
     */
    private static function merge(ValueMap ...$valueMapList): ValueMap
    {
        /** @var array<string, \vinyl\di\definition\DefinitionValue> $result */
        $result = [];

        foreach ($valueMapList as $values) {
            /** @var string $argumentName */
            foreach ($values as $argumentName => $value) {
                if (!array_key_exists($argumentName, $result)) {
                    $result[$argumentName] = clone $value;
                    continue;
                }

                /** @var \vinyl\di\definition\DefinitionValue $currentValue */
                $currentValue = $result[$argumentName];
                if ($value instanceof Mergeable && $currentValue instanceof Mergeable) {
                    $result[$argumentName] = $currentValue->merge($value);
                    continue;
                }

                $result[$argumentName] = clone $value;
            }
        }

        return new ValueMap($result);
    }
}
