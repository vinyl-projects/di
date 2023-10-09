<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

/**
 * Class IncompatibleTypeException
 */
final class IncompatibleTypeException extends ValueProcessorException
{
    /**
     * Static constructor of {@see IncompatibleTypeException}
     */
    public static function create(?ReflectionType $expected, string $given): self
    {
        $renderedExpectedType = self::renderType($expected);
        return new self("Type [{$given}] defined in definition is incompatible with required one [{$renderedExpectedType}].");
    }

    private static function renderType(?ReflectionType $expected): string
    {
        if ($expected === null) {
            return 'mixed;';
        }

        if ($expected instanceof ReflectionNamedType) {
            return $expected->getName();
        }

        if ($expected instanceof ReflectionUnionType) {
            $result = [];
            foreach ($expected->getTypes() as $type) {
                $result[] = self::renderType($type);
            }

            return '(' . implode('|', $result) . ')';
        }

        if ($expected instanceof ReflectionIntersectionType) {
            $result = [];
            foreach ($expected->getTypes() as $type) {
                $result[] = self::renderType($type);
            }

            return '(' . implode('&', $result) . ')';
        }

        return '';
    }
}
