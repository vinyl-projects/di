<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\Definition;
use vinyl\di\definition\ClassResolver;
use vinyl\di\definition\ClassResolverAware;
use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionDependency;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\NullValueException;
use vinyl\di\definition\ObjectValue;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\DefinitionFactoryValue;
use vinyl\di\ShadowClassDefinition;
use vinyl\std\lang\collections\Map;
use function assert;
use function is_string;
use function vinyl\di\isDeclaredTypeCompatibleWith;
use function vinyl\std\lang\collections\vectorOf;

/**
 * Class ObjectValueProcessor
 */
final class ObjectValueProcessor implements ValueProcessor, ClassResolverAware
{
    private ?ClassResolver $classResolver = null;

    /**
     * {@inheritDoc}
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        Map $definitionMap
    ): ValueProcessorResult {
        assert($value instanceof ObjectValue);
        assert($this->classResolver !== null);

        $definitionId = $value->value();

        if ($definitionId === null && !$constructorValue->isNullable()) {
            throw NullValueException::create();
        }

        if ($definitionId === null) {
            return new ValueProcessorResult(new DefinitionFactoryValue(null, false), null);
        }

        $definition = self::resolveDefinition($definitionMap, $value);
        $objectTypeValue = new DefinitionFactoryValue($definition->id(), false);
        $resolvedClass = $this->classResolver->resolve($definition, $definitionMap);

        $object_or_class = $resolvedClass->name();
        if (!isDeclaredTypeCompatibleWith($constructorValue->type(), $object_or_class)) {
            throw IncompatibleTypeException::create($constructorValue->type(), "{$object_or_class} -> {$definitionId}");
        }

        return new ValueProcessorResult($objectTypeValue, vectorOf(DefinitionDependency::create($definition)));
    }

    /**
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     */
    private static function resolveDefinition(Map $definitionMap, ObjectValue $typeValue): Definition
    {
        $value = $typeValue->value();
        assert(is_string($value));

        if ($definitionMap->containsKey($value)) {
            return $definitionMap->get($value);
        }

        return ShadowClassDefinition::resolveShadowDefinition($value, $definitionMap);
    }

    /**
     * {@inheritDoc}
     */
    public function injectClassResolver(ClassResolver $resolver): void
    {
        $this->classResolver = $resolver;
    }
}
