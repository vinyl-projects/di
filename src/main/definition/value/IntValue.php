<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

/**
 * Class IntValue
 */
final readonly class IntValue implements \vinyl\di\definition\IntValue
{
    /**
     * IntValue constructor.
     */
    public function __construct(private ?int $value)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?int
    {
        return $this->value;
    }
}
