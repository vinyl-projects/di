<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

/**
 * Class BoolValue
 */
final class BoolValue implements \vinyl\di\definition\BoolValue
{
    private ?bool $value;

    /**
     * BoolValue constructor.
     */
    public function __construct(?bool $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?bool
    {
        return $this->value;
    }
}
