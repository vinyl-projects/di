<?php

declare(strict_types=1);

namespace vinyl\di\factory\argument;

use vinyl\di\factory\FactoryValue;

/**
 * Class BuiltinFactoryValue
 */
final class BuiltinFactoryValue implements FactoryValue
{
    /** @var mixed */
    private $value;
    private bool $isMissed;

    /**
     * MixedValue constructor.
     *
     * @param mixed $value
     */
    public function __construct($value, bool $isMissed)
    {
        $this->value = $value;
        $this->isMissed = $isMissed;
    }

    /**
     * {@inheritDoc}
     */
    public function isMissed(): bool
    {
        return $this->isMissed;
    }

    /**
     * {@inheritDoc}
     */
    public function value()
    {
        return $this->value;
    }
}
