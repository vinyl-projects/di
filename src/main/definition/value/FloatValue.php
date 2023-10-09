<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

/**
 * Class FloatValue
 */
final readonly class FloatValue implements \vinyl\di\definition\FloatValue
{
    /**
     * FloatValue constructor.
     */
    public function __construct(private ?float $value)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?float
    {
        return $this->value;
    }
}
