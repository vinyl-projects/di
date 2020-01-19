<?php

declare(strict_types=1);

namespace vinyl\di\definition\arrayValue;

use vinyl\di\definition\OrderableValue;
use vinyl\di\definition\value\IntValue;

/**
 * Class OrderableIntValue
 */
final class OrderableIntValue implements OrderableValue, \vinyl\di\definition\IntValue
{
    private int $sortOrder;
    private IntValue $intValue;

    /**
     * IntValue constructor.
     */
    public function __construct(?int $value, ?int $sortOrder = null)
    {
        $this->intValue = new IntValue($value);
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
    public function value(): ?int
    {
        return $this->intValue->value();
    }

    public function __clone()
    {
        $this->intValue = clone $this->intValue;
    }
}
