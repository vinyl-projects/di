<?php

declare(strict_types=1);

namespace vinyl\di\definition\constructorMetadata;

/**
 * Class NamedObjectConstructorValue
 */
final class NamedObjectConstructorValue implements ConstructorValue
{
    private string $type;
    private bool $isNullable;
    private bool $isOptional;
    private bool $isInterface;
    private bool $isAbstract;
    private bool $isVariadic;

    /**
     * NamedObjectConstructorValue constructor.
     */
    public function __construct(
        string $type,
        bool $isNullable,
        bool $isOptional,
        bool $isInterface,
        bool $isAbstract,
        bool $isVariadic
    ) {
        $this->type = $type;
        $this->isNullable = $isNullable;
        $this->isOptional = $isOptional;
        $this->isInterface = $isInterface;
        $this->isAbstract = $isAbstract;
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
        return null;
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

    public function isInterface(): bool
    {
        return $this->isInterface;
    }

    public function isAbstractClass(): bool
    {
        return $this->isAbstract;
    }

    /**
     * {@inheritDoc}
     */
    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }
}
