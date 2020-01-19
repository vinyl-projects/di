<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

/**
 * Class FloatValue
 */
final class FloatValue implements \vinyl\di\definition\FloatValue
{
    private ?float $value;

    /**
     * FloatValue constructor.
     */
    public function __construct(?float $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?float
    {
        return $this->value;
    }
}
