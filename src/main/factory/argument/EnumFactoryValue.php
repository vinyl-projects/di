<?php

declare(strict_types=1);

namespace vinyl\di\factory\argument;

use vinyl\di\factory\FactoryValue;

final class EnumFactoryValue implements FactoryValue
{

    public function __construct(private readonly ?string $enumClass, private readonly ?string $caseName, private readonly bool $isMissed)
    {
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
    public function value(): ?string
    {
        return $this->caseName;
    }

    /**
     * {@inheritDoc}
     */
    public function type(): ?string
    {
        return $this->enumClass;
    }
}
