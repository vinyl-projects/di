<?php

declare(strict_types=1);

namespace vinyl\di\definition\constructorMetadata;

/**
 * Class BuiltinConstructorValue
 */
final class BuiltinConstructorValue implements ConstructorValue
{
    /** @var string|float|bool|int|null|array<int|string, mixed> */
    private $defaultValue;
    private string $type;
    private bool $isNullable;
    private bool $isOptional;
    private bool $isVariadic;

    /**
     * ConstructorValueMetadata constructor.
     *
     * @param string|float|bool|int|null|array<int|string, mixed> $defaultValue
     */
    public function __construct(
        $defaultValue,
        string $type,
        bool $isNullable,
        bool $isDefaultValueAvailable,
        bool $isVariadic
    ) {
        $this->type = $type;
        $this->isNullable = $isNullable;
        $this->defaultValue = $defaultValue;
        $this->isOptional = $isDefaultValueAvailable;
        $this->isVariadic = $isVariadic;
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
     */
    public function defaultValue()
    {
        return $this->defaultValue;
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
