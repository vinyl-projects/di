<?php

declare(strict_types=1);

namespace vinyl\di\definition\constructorMetadata;

use ReflectionType;

final readonly class ConstructorValue
{
    public function __construct(
        private mixed           $defaultValue,
        private ?ReflectionType $type,
        private bool            $isNullable,
        private bool            $isOptional,
        private bool            $isVariadic
    )
    {
    }

    /**
     * Returns the {@see ReflectionType} of the constructor value or NULL if type is not declared
     */
    public function type(): ?ReflectionType
    {
        return $this->type;
    }

    /**
     * Returns default value
     */
    public function defaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }
}
