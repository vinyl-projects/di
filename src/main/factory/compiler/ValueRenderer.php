<?php

declare(strict_types=1);

namespace vinyl\di\factory\compiler;

use vinyl\di\factory\FactoryValue;

/**
 * Interface ValueRenderer
 */
interface ValueRenderer
{
    public function render(FactoryValue $value): string;
}
