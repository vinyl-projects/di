<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use Psr\Container\ContainerInterface;
use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\constructorMetadata\NamedObjectConstructorValue;
use vinyl\di\definition\DefinitionToDependencyMap;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\UnmodifiableDefinitionMap;
use vinyl\di\definition\value\NoValue;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\BuiltinFactoryValue;
use vinyl\di\factory\argument\DefinitionFactoryValue;
use vinyl\di\ShadowClassDefinition;
use function assert;

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
        UnmodifiableDefinitionMap $definitionMap
    ): ValueProcessorResult {
        assert($value instanceof NoValue);

        $isOptional = $constructorValue->isOptional();
        if (!$constructorValue instanceof NamedObjectConstructorValue) {
            return new ValueProcessorResult(new BuiltinFactoryValue($constructorValue->defaultValue(), !$isOptional));
        }

        if ($isOptional) {
            return new ValueProcessorResult(new DefinitionFactoryValue(null, false));
        }

        $type = $constructorValue->type();
        if ($constructorValue->isInterface()) {
            $definitionBoolMap = null;
            if ($definitionMap->contains($type)) {
                $definitionBoolMap = new DefinitionToDependencyMap();
                $definitionBoolMap->insert($definitionMap->get($type), true);
            }

            $isMissed = !$definitionMap->contains($type) && $type !== ContainerInterface::class;

            return new ValueProcessorResult(
                new DefinitionFactoryValue($type, $isMissed),
                $definitionBoolMap
            );
        }

        $classDefinition = $definitionMap->contains($type)
            ? $definitionMap->get($type)
            : ShadowClassDefinition::resolveShadowDefinition($type, $definitionMap);

        $definitionBoolMap = new DefinitionToDependencyMap();
        $definitionBoolMap->insert($classDefinition, true);

        return new ValueProcessorResult(
            new DefinitionFactoryValue($classDefinition->id(), $constructorValue->isAbstractClass()),
            $definitionBoolMap
        );
    }
}
