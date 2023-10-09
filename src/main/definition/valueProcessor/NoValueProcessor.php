<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use Psr\Container\ContainerInterface;
use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionDependency;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\value\NoValue;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\BuiltinFactoryValue;
use vinyl\di\factory\argument\DefinitionFactoryValue;
use vinyl\di\factory\argument\EnumFactoryValue;
use vinyl\di\ShadowClassDefinition;
use vinyl\std\lang\collections\Map;
use function assert;
use function vinyl\std\lang\collections\vectorOf;

/**
 * Class NoValueProcessor
 */
final class NoValueProcessor implements ValueProcessor
{
    /**
     * @inheritDoc
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        Map $definitionMap
    ): ValueProcessorResult {
        assert($value instanceof NoValue);

        $isOptional = $constructorValue->isOptional();
        $isMissed = !$isOptional;

        $type = $constructorValue->type();

        $defaultValue = $constructorValue->defaultValue();
        if ($type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
            return new ValueProcessorResult(new BuiltinFactoryValue($defaultValue, $isMissed));
        }

        if (!$type instanceof \ReflectionNamedType) {
            throw new \RuntimeException('Unknown reflection type.');
        }

        if ($type->isBuiltin()) {
            return new ValueProcessorResult(new BuiltinFactoryValue($defaultValue, $isMissed));
        }

        $typeName = $type->getName();
        $classReflection = new \ReflectionClass($typeName);
        if ($classReflection->isEnum()) {
            assert($defaultValue instanceof \UnitEnum || $defaultValue === null);
            return new ValueProcessorResult(new EnumFactoryValue($typeName, $defaultValue?->name, $isMissed));
        }

        if ($isOptional) {
            return new ValueProcessorResult(new DefinitionFactoryValue(null, false));
        }

        if ($classReflection->isInterface()) {
            $dependencies = null;
            if ($definitionMap->containsKey($typeName)) {
                $dependencies = vectorOf(DefinitionDependency::create($definitionMap->get($typeName)));
            }

            $isMissed = !$definitionMap->containsKey($typeName) && $typeName !== ContainerInterface::class;

            return new ValueProcessorResult(
                new DefinitionFactoryValue($typeName, $isMissed),
                $dependencies
            );
        }

        $classDefinition = $definitionMap->containsKey($typeName)
            ? $definitionMap->get($typeName)
            : ShadowClassDefinition::resolveShadowDefinition($typeName, $definitionMap);

        return new ValueProcessorResult(
            new DefinitionFactoryValue($classDefinition->id(), $classReflection->isAbstract()),
            vectorOf(DefinitionDependency::create($classDefinition))
        );
    }
}
