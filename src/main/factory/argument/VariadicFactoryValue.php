<?php

declare(strict_types=1);

namespace vinyl\di\factory\argument;

use vinyl\di\factory\FactoryValue;

/**
 * Class VariadicFactoryValue
 */
final class VariadicFactoryValue implements FactoryValue
{
    /** @var \vinyl\di\factory\FactoryValue[] */
    private ?array $value;
    private bool $isMissed;

    /**
     * VariadicValue constructor.
     *
     * @param \vinyl\di\factory\FactoryValue[]|null $value
     */
    public function __construct(?array $value, bool $isMissed)
    {
        $this->value = $value;
        $this->isMissed = $isMissed;
    }

    /**
     * @return FactoryValue[]
     */
    public function value(): ?array
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function isMissed(): bool
    {
        return $this->isMissed;
    }
}
