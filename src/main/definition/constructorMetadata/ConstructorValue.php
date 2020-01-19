<?php

declare(strict_types=1);

namespace vinyl\di\definition\constructorMetadata;

/**
 * Interface ConstructorValue
 */
interface ConstructorValue
{
    /**
     * Returns type of current {@see ConstructorValue}
     */
    public function type(): string;

    /**
     * @return mixed
     */
    public function defaultValue();

    /**
     *
     */
    public function isNullable(): bool;

    public function isOptional(): bool;

    public function isVariadic(): bool;
}
