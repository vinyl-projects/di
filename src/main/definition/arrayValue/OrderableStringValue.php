<?php

declare(strict_types=1);

namespace vinyl\di\definition\arrayValue;

use vinyl\di\definition\OrderableValue;
use vinyl\di\definition\value\StringValue;

/**
 * Class OrderableStringValue
 */
final class OrderableStringValue implements OrderableValue, \vinyl\di\definition\StringValue
{
    private int $sortOrder;
    private StringValue $stringValue;

    /**
     * StringValue constructor.
     */
    public function __construct(?string $value, ?int $sortOrder = null)
    {
        $this->stringValue = new StringValue($value);
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
    public function value(): ?string
    {
        return $this->stringValue->value();
    }

    public function __clone()
    {
        $this->stringValue = clone $this->stringValue;
    }
}
