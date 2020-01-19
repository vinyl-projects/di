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
 * Class ConstructorMetadataExtractor
 * todo Extract to interface
 */
final class ConstructorMetadataExtractor
{
    /**
     * @param string|null $constructorMethod static method name or null to use default constructor
     *
     * @return array<string, \vinyl\di\definition\constructorMetadata\ConstructorValue>
     * @throws \vinyl\di\definition\ConstructorMetadataExtractorException top level exception of {@see ConstructorMetadataExtractor}
     * @throws \vinyl\di\definition\InvalidConstructorMethodException is thrown if given constructor method could not be used as constructor
     * @throws \vinyl\di\definition\ArgumentTypeNotFoundException is thrown if given class constructor contains parameter with undefined class
     */
    public function extract(string $className, ?string $constructorMethod): array
    {
        assert($constructorMethod !== '');
        assert(class_exists($className));

        $isStaticConstructor = $constructorMethod !== null;
        $constructorMethod = $constructorMethod ?? '__construct';
        try {
            $classReflection = new ReflectionClass($className);

            if (!$classReflection->hasMethod($constructorMethod)) {
                if ($isStaticConstructor) {
                    throw new InvalidConstructorMethodException("Method [{$constructorMethod}] not exists.");
                }

                return [];
            }

            $constructor = $classReflection->getMethod($constructorMethod);
        } catch (ReflectionException $e) {
            throw new Error('Impossible reflection exception. Details: ' . $e->getMessage(), $e->getCode(), $e);
        }

        if (!$constructor->isPublic()) {
            throw new InvalidConstructorMethodException("Method [{$constructorMethod}] must not be private or protected.");
        }

        if ($isStaticConstructor && !$constructor->isStatic()) {
            throw new InvalidConstructorMethodException("Method [{$constructorMethod}] must be static.");
        }

        $result = [];

        foreach ($constructor->getParameters() as $parameter) {
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
     * @return mixed
     */
    private static function extractDefaultValue(ReflectionParameter $parameter)
    {
        #todo drop isDefaultValueAvailable ???
        if (!$parameter->isOptional() || !$parameter->isDefaultValueAvailable()) {
            return null;
        }

        try {
            return $parameter->getDefaultValue();
        } catch (ReflectionException $e) {
            throw new Error('Impossible reflection exception. Details ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
