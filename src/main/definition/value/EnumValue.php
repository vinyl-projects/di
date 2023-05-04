<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

final class EnumValue implements \vinyl\di\definition\EnumValue
{

    public function __construct(private ?\UnitEnum $value)
    {
    }

    public function value(): ?\UnitEnum
    {
        return $this->value;
    }
}
