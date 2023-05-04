<?php

declare(strict_types=1);

namespace vinyl\di\definition\constructorMetadata;

final class EnumConstructorValue implements ConstructorValue
{

    /**
     * EnumConstructorValue constructor.
     */
    public function __construct(
        private readonly string  $type,
        private readonly ?string $enumCaseName,
        private readonly bool    $isNullable,
        private readonly bool    $isOptional,
        private readonly bool    $isVariadic
    )
    {
    }

    /**
     * {@inheritDoc}
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     *
     * @return null|string
     */
    public function defaultValue()
    {
        return $this->enumCaseName;
    }

    /**
     * {@inheritDoc}
     */
    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * {@inheritDoc}
     */
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * {@inheritDoc}
     */
    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }
}
