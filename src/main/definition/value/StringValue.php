<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

/**
 * Class StringValue
 */
final readonly class StringValue implements \vinyl\di\definition\StringValue
{
    /**
     * StringValue constructor.
     */
    public function __construct(private ?string $value)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?string
    {
        return $this->value;
    }
}
