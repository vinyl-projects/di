<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

/**
 * Class StringValue
 */
final class StringValue implements \vinyl\di\definition\StringValue
{
    private ?string $value;

    /**
     * StringValue constructor.
     */
    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?string
    {
        return $this->value;
    }
}
