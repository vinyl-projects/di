<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Class NullValueException
 */
final class NullValueException extends ValueProcessorException
{
    /**
     * Static constructor of {@see NullValueException}
     */
    public static function create(): self
    {
        return new self('Null value is not allowed.');
    }
}
