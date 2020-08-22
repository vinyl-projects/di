<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use Error;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use vinyl\di\definition\constructorMetadata\BuiltinConstructorValue;
use vinyl\di\definition\constructorMetadata\NamedObjectConstructorValue;
use function assert;
use function class_exists;
use function interface_exists;

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
            /** @var string $parameterName */
            $parameterName = $parameter->name;
            /** @var \ReflectionNamedType|null $parameterType */
            $parameterType = $parameter->getType();
            $isOptional = $parameter->isOptional();
            $isNullable = $parameterType === null || $parameterType->allowsNull();
            $defaultValue = self::extractDefaultValue($parameter);

            $type = $parameterType === null ? 'mixed' : $parameterType->getName();
            if ($parameterType === null || $parameterType->isBuiltin()) {
                $result[$parameterName] = new BuiltinConstructorValue(
                    $defaultValue,
                    $type,
                    $isNullable,
                    $isOptional,
                    $parameter->isVariadic()
                );

                continue;
            }

            assert(class_exists($type) || interface_exists($type));
            try {
                $parameterClassReflection = new ReflectionClass($type);
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
     * @return string|float|bool|int|null|array<int|string, mixed>
     */
    private static function extractDefaultValue(ReflectionParameter $parameter)
    {
        #todo drop isDefaultValueAvailable ???
        if (!$parameter->isOptional() || !$parameter->isDefaultValueAvailable()) {
            return null;
        }

        try {
            $defaultValue = $parameter->getDefaultValue();
            assert(
                is_scalar($defaultValue)
                || is_null($defaultValue)
                || is_array($defaultValue)
            );

            return $defaultValue;
        } catch (ReflectionException $e) {
            throw new Error("Impossible reflection exception. Details {$e->getMessage()}", 0, $e);
        }
    }
}
