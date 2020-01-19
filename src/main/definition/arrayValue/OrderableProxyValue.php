<?php

declare(strict_types=1);

namespace vinyl\di\definition\arrayValue;

use vinyl\di\definition\OrderableValue;
use vinyl\di\definition\value\ProxyValue;

/**
 * Class OrderableProxyValue
 */
final class OrderableProxyValue implements \vinyl\di\definition\ProxyValue, OrderableValue
{
    private int $sortOrder;
    private ProxyValue $proxyValue;

    /**
     * ProxyValue constructor.
     */
    public function __construct(?string $definitionId, ?int $sortOrder = null)
    {
        $this->sortOrder = $sortOrder ?? self::DEFAULT_SORT_ORDER;
        $this->proxyValue = new ProxyValue($definitionId);
    }

    /**
     * {@inheritDoc}
     */
    public function order(): int
    {
        return $this->sortOrder;
    }

    /**
     * Returns definition id, if returned value is null then definition id should be taken from constructor parameter
     */
    public function value(): ?string
    {
        return $this->proxyValue->value();
    }

    public function __clone()
    {
        $this->proxyValue = clone $this->proxyValue;
    }
}
