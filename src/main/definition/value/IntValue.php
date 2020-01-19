<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

/**
 * Class IntValue
 */
final class IntValue implements \vinyl\di\definition\IntValue
{
    private ?int $value;

    /**
     * IntValue constructor.
     */
    public function __construct(?int $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?int
    {
        return $this->value;
    }
}
