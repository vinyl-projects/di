<?php

declare(strict_types=1);

namespace vinyl\di\definition\arrayValue;

use vinyl\di\definition\OrderableValue;
use vinyl\di\definition\value\ObjectValue;

/**
 * Class OrderableObjectValue
 */
final class OrderableObjectValue implements OrderableValue, \vinyl\di\definition\ObjectValue
{
    private int $order;
    private ObjectValue $objectValue;

    /**
     * ObjectValue constructor.
     */
    public function __construct(?string $definitionId, ?int $order = null)
    {
        $this->objectValue = new ObjectValue($definitionId);
        $this->order = $order ?? self::DEFAULT_SORT_ORDER;
    }

    /**
     * {@inheritDoc}
     */
    public function order(): int
    {
        return $this->order;
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?string
    {
        return $this->objectValue->value();
    }

    public function __clone()
    {
        $this->objectValue = clone $this->objectValue;
    }
}
