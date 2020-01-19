<?php

declare(strict_types=1);

namespace vinyl\di\factory\argument;

use vinyl\di\factory\FactoryValue;

/**
 * Class ArrayValue
 */
final class ArrayValue implements FactoryValue
{
    /** @var array<string|int, FactoryValue> */
    private ?array $value;
    private bool $isMissed;

    /**
     * ArrayValue constructor.
     *
     * @param array<string|int, FactoryValue>|null $value
     */
    public function __construct(?array $value, bool $isMissed)
    {
        $this->value = $value;
        $this->isMissed = $isMissed;
    }

    public static function createNullValue(): self
    {
        return new self(null, false);
    }

    /**
     * @return array<string|int, FactoryValue>|null
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
