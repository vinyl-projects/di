<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Class IncompatibleTypeException
 */
final class IncompatibleTypeException extends ValueProcessorException
{
    /**
     * Static constructor of {@see IncompatibleTypeException}
     */
    public static function create(string $expected, string $given): self
    {
        return new self("Type [{$given}] defined in definition is incompatible with required one [{$expected}].");
    }
}
