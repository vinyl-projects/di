<?php

declare(strict_types=1);

namespace vinyl\di\definition\arrayValue;

use vinyl\di\definition\OrderableValue;
use vinyl\di\definition\value\BoolValue;

/**
 * Class OrderableBoolValue
 */
final class OrderableBoolValue implements OrderableValue, \vinyl\di\definition\BoolValue
{
    private int $sortOrder;
    private BoolValue $boolValue;

    /**
     * BoolValue constructor.
     */
    public function __construct(?bool $value, ?int $sortOrder = null)
    {
        $this->boolValue = new BoolValue($value);
        $this->sortOrder = $sortOrder ?? self::DEFAULT_SORT_ORDER;
    }

    /**
     * {@inheritDoc}
     */
    public function order(): int
    {
        return $this->sortOrder;
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?bool
    {
        return $this->boolValue->value();
    }

    public function __clone()
    {
        $this->boolValue = clone $this->boolValue;
    }
}
