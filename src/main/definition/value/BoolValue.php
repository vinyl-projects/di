<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

/**
 * Class BoolValue
 */
final readonly class BoolValue implements \vinyl\di\definition\BoolValue
{
    /**
     * BoolValue constructor.
     */
    public function __construct(private ?bool $value)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?bool
    {
        return $this->value;
    }
}
