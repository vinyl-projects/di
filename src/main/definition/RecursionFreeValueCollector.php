<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use SplStack;
use vinyl\di\AliasOnAliasDefinition;
use vinyl\di\Definition;
use vinyl\di\definition\value\Mergeable;
use vinyl\std\lang\collections\Map;
use vinyl\std\lang\collections\MutableMap;
use function array_reverse;
use function count;
use function vinyl\std\lang\collections\mapOf;
use function vinyl\std\lang\collections\mutableMapOf;

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
    public function collect(Definition $definition, Map $definitionMap): Map
    {
        /** @var MutableMap<string, \vinyl\di\definition\DefinitionValue>[] $valueMapList */
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
            return mapOf();
        }

        return self::merge(array_reverse($valueMapList));
    }

    /**
     * Merge given {@see MutableMap } with one or more {@see MutableMap }
     *
     * If several value maps are passed, they will be processed in order, the later map overwriting the previous.
     *
     * If {@see FactoryValue } implements {@see Mergeable} interface it will be merged with value from other map
     *
     * @psalm-param iterable<MutableMap<string, \vinyl\di\definition\DefinitionValue>> $valueMapList
     *
     * @return Map<string, \vinyl\di\definition\DefinitionValue>
     */
    private static function merge(iterable $valueMapList): Map
    {
        /** @var \vinyl\std\lang\collections\MutableMap<string, \vinyl\di\definition\DefinitionValue> $valueMap */
        $valueMap = mutableMapOf();

        foreach ($valueMapList as $values) {
            /** @var string $argumentName */
            foreach ($values as $argumentName => $value) {
                if (!$valueMap->containsKey($argumentName)) {
                    $valueMap->put($argumentName, clone $value);
                    continue;
                }

                /** @var \vinyl\di\definition\DefinitionValue $currentValue */
                $currentValue = $valueMap->get($argumentName);
                if ($value instanceof Mergeable && $currentValue instanceof Mergeable) {
                    $valueMap->put($argumentName, $currentValue->merge($value));
                    continue;
                }

                $valueMap->put($argumentName, clone $value);
            }
        }

        return $valueMap;
    }
}
