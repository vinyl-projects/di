<?php declare(strict_types=1);

namespace vinyl\di;

use InvalidArgumentException;
use function strlen;
use function strrpos;
use function substr;

/**
 * Extracts from full class name (with namespace) short class name and namespace.
 *
 * This function does not use reflection, so that it could be used even if class not exist.
 *
 * Ex: \Some\Class\Name class name will be split into 2 parts: 'Name' and 'Some\Class'
 *
 * @param string $fullyQualifiedClassName Valid fully qualified class name
 *
 * @return string[] an array with 2 elements, first is short class name, the second is namespace (namespace could be an empty string)
 * @psalm-return array{0: string, 1: string}
 */
function classExtractShortNameAndNamespace(string $fullyQualifiedClassName): array
{
    if ($fullyQualifiedClassName === '') {
        throw new InvalidArgumentException('Class name could not be empty.');
    }

    $lastBackslashPosition = strrpos($fullyQualifiedClassName, '\\', -1);

    if ((strlen($fullyQualifiedClassName) - 1) === $lastBackslashPosition) {
        throw new InvalidArgumentException('Invalid class name provided. Last symbol could not be equals to backslash "\".');
    }

    if ($lastBackslashPosition === false) {
        return [$fullyQualifiedClassName, ''];
    }

    $namespace = substr($fullyQualifiedClassName, 0, $lastBackslashPosition);
    $shortClassName = substr($fullyQualifiedClassName, ++$lastBackslashPosition);

    return [$shortClassName, $namespace];
}

function isDeclaredTypeCompatibleWith(?\ReflectionType $declaredType, string $givenTypeName): bool
{
    if ($declaredType === null) {//no type declared (mixed)
        return true;
    }

    if ($declaredType instanceof \ReflectionNamedType) {
        $declaredTypeName = $declaredType->getName();

        if ($declaredTypeName === 'mixed') {
            return true;
        }

        if ($declaredType->isBuiltin()) {
            if ($declaredTypeName === 'float' && $givenTypeName === 'int') {
                return true;
            }

            if ($declaredTypeName === 'object') {
                return class_exists($givenTypeName);
            }

            return $declaredTypeName === $givenTypeName;
        }

        if (is_a($givenTypeName, $declaredTypeName, true)) {
            return true;
        }
    }

    if ($declaredType instanceof \ReflectionUnionType) {
        foreach ($declaredType->getTypes() as $type) {
            $isCompatible = isDeclaredTypeCompatibleWith($type, $givenTypeName);
            if ($isCompatible) {
                return true;
            }
        }

        return false;
    }

    if ($declaredType instanceof \ReflectionIntersectionType) {
        foreach ($declaredType->getTypes() as $type) {
            $isCompatible = isDeclaredTypeCompatibleWith($type, $givenTypeName);

            if ($isCompatible === false) {
                return false;
            }
        }

        return true;
    }

    return false;
}