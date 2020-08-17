<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\Definition;
use vinyl\di\definition\ClassResolver;
use vinyl\di\definition\ClassResolverAware;
use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionToDependencyMap;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\NullValueException;
use vinyl\di\definition\ObjectValue;
use vinyl\di\definition\UnmodifiableDefinitionMap;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\DefinitionFactoryValue;
use vinyl\di\ShadowClassDefinition;
use function assert;
use function is_a;
use function is_string;

/**
 * Class ObjectValueProcessor
 */
final class ObjectValueProcessor implements ValueProcessor, ClassResolverAware
{
    private ?ClassResolver $classResolver = null;

    /**
     * {@inheritDoc}
     *
     * @param ObjectValue $value
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        UnmodifiableDefinitionMap $definitionMap
    ): ValueProcessorResult {
        assert($value instanceof ObjectValue);
        assert($this->classResolver !== null);

        $definitionId = $value->value();
        $type = $constructorValue->type();

        if ($definitionId === null && !$constructorValue->isNullable()) {
            throw NullValueException::create();
        }

        $definitionBoolMap = new DefinitionToDependencyMap();
        if ($definitionId === null) {
            return new ValueProcessorResult(new DefinitionFactoryValue(null, false), null);
        }

        $definition = self::resolveDefinition($definitionMap, $value);
        $objectTypeValue = new DefinitionFactoryValue($definition->id(), false);
        $resolvedClass = $this->classResolver->resolve($definition, $definitionMap);

        if (!is_a($resolvedClass->name(), $type, true) && $type !== 'object' && $type !== 'mixed') {
            throw IncompatibleTypeException::create($type, "{$resolvedClass->name()} -> {$definitionId}");
        }

        $definitionBoolMap->insert($definition, true);

        return new ValueProcessorResult($objectTypeValue, $definitionBoolMap);
    }

    private static function resolveDefinition(UnmodifiableDefinitionMap $definitionMap, ObjectValue $typeValue): Definition
    {
        assert(is_string($typeValue->value()));

        if ($definitionMap->contains($typeValue->value())) {
            return $definitionMap->get($typeValue->value());
        }

        return ShadowClassDefinition::resolveShadowDefinition($typeValue->value(), $definitionMap);
    }

    /**
     * {@inheritDoc}
     */
    public function injectClassResolver(ClassResolver $resolver): void
    {
        $this->classResolver = $resolver;
    }
}
