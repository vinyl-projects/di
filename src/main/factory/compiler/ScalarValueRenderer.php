<?php

declare(strict_types=1);

namespace vinyl\di\factory\compiler;

use vinyl\di\factory\FactoryValue;
use function var_export;

/**
 * Class ScalarValueRenderer
 */
final class ScalarValueRenderer implements ValueRenderer
{
    /**
     * {@inheritDoc}
     */
    public function render(FactoryValue $value): string
    {
        return var_export($value->value(), true);
    }
}
