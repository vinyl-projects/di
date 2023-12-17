<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use ReflectionParameter;
use vinyl\di\definition\constructorMetadata\ConstructorValue;

/**
 * Class ConstructorValueExtractor
 * todo Extract interface
 */
final class ConstructorValueExtractor
{
    /**
     * @return array<string, ConstructorValue>
     */
    public function extract(Instantiator $objectInstantiator): array
    {
        $result = [];

        foreach ($objectInstantiator->parameters() as $parameter) {
            $parameterName = $parameter->name;
            $parameterType = $parameter->getType();
            $isOptional = $parameter->isOptional();
            $isNullable = $parameterType === null || $parameterType->allowsNull();
            /** @var mixed $defaultValue */
            $defaultValue = self::extractDefaultValue($parameter);

            $result[$parameterName] = new ConstructorValue(
                $defaultValue,
                $parameterType,
                $isNullable,
                $isOptional,
                $parameter->isVariadic()
            );
        }

        return $result;
    }

    /**
     * Extract default value from parameter if parameter is optional, otherwise returns null
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
