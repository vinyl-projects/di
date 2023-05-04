<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use vinyl\di\definition\constructorMetadata\BuiltinConstructorValue;
use vinyl\di\definition\constructorMetadata\EnumConstructorValue;
use vinyl\di\definition\constructorMetadata\NamedObjectConstructorValue;

/**
 * Class ConstructorValueExtractor
 * todo Extract interface
 */
final class ConstructorValueExtractor
{
    /**
     * @return array<string, \vinyl\di\definition\constructorMetadata\ConstructorValue>
     * @throws \vinyl\di\definition\ConstructorMetadataExtractorException top level exception of {@see ConstructorValueExtractor}
     * @throws \vinyl\di\definition\ArgumentTypeNotFoundException is thrown if given class constructor contains parameter with undefined class
     */
    public function extract(Instantiator $objectInstantiator): array
    {
        $result = [];

        foreach ($objectInstantiator->parameters() as $parameter) {
            $parameterName = $parameter->name;
            /** @var \ReflectionNamedType|null $parameterType */
            $parameterType = $parameter->getType();
            $isOptional = $parameter->isOptional();
            $isNullable = $parameterType === null || $parameterType->allowsNull();
            $defaultValue = self::extractDefaultValue($parameter);

            $type = $parameterType === null ? 'mixed' : $parameterType->getName();
            if ($parameterType === null || $parameterType->isBuiltin()) {
                assert(is_scalar($defaultValue)
                    || is_array($defaultValue)
                    || is_object($defaultValue)
                    || is_null($defaultValue),
                    get_debug_type($defaultValue)
                );
                assert(!is_object($defaultValue));
                $result[$parameterName] = new BuiltinConstructorValue(
                    $defaultValue,
                    $type,
                    $isNullable,
                    $isOptional,
                    $parameter->isVariadic()
                );

                continue;
            }

            try {
                /** @var class-string $type */
                $parameterClassReflection = new ReflectionClass($type);
                if ($parameterClassReflection->isEnum()) {
                    /** @var \UnitEnum $defaultValue */
                    $result[$parameterName] = new EnumConstructorValue($type, $defaultValue->name, $isNullable, $isOptional, $parameter->isVariadic());
                    continue;
                }

                $result[$parameterName] = new NamedObjectConstructorValue(
                    $type,
                    $isNullable,
                    $isOptional,
                    $parameterClassReflection->isInterface(),
                    ($parameterClassReflection->isAbstract() && !$parameterClassReflection->isInterface()),
                    $parameter->isVariadic()
                );
            } catch (ReflectionException $e) {
                throw ArgumentTypeNotFoundException::create($type, $parameterName);
            }
        }

        return $result;
    }

    /**
     * Extract default value from parameter if parameter is optional, otherwise returns null
     *
     * @param ReflectionParameter $parameter
     *
     * @return mixed
     */
    private static function extractDefaultValue(ReflectionParameter $parameter): mixed
    {
        #todo drop isDefaultValueAvailable ???
        if (!$parameter->isOptional() || !$parameter->isDefaultValueAvailable()) {
            return null;
        }

        return $parameter->getDefaultValue();
    }
}
