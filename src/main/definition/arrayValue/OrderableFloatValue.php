<?php

declare(strict_types=1);

namespace vinyl\di\definition\arrayValue;

use vinyl\di\definition\OrderableValue;
use vinyl\di\definition\value\FloatValue;

/**
 * Class OrderableFloatValue
 */
final class OrderableFloatValue implements OrderableValue, \vinyl\di\definition\FloatValue
{
    private int $sortOrder;
    private FloatValue $floatValue;

    /**
     * FloatValue constructor.
     */
    public function __construct(?float $value, ?int $sortOrder = null)
    {
        $this->floatValue = new FloatValue($value);
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
    public function value(): ?float
    {
        return $this->floatValue->value();
    }

    public function __clone()
    {
        $this->floatValue = clone $this->floatValue;
    }
}
