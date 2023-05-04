<?php

declare(strict_types=1);

namespace vinyl\di\factory\compiler;

use vinyl\di\factory\argument\EnumFactoryValue;
use vinyl\di\factory\FactoryValue;

final class EnumValueRenderer implements ValueRenderer
{
    /**
     * {@inheritDoc}
     */
    public function render(FactoryValue $value): string
    {
        assert($value instanceof EnumFactoryValue);
        if ($value->value() === null) {
            return 'null';
        }

        return "\\{$value->type()}::{$value->value()}";
    }
}
