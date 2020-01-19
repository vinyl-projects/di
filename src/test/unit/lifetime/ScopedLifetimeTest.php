<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\lifetime;

use vinyl\di\definition\Lifetime;
use vinyl\di\definition\ScopedLifetime;

/**
 * Class ScopedLifetimeTest
 */
class ScopedLifetimeTest extends LifetimeTest
{
    protected function createLifetime(): Lifetime
    {
        return ScopedLifetime::get();
    }
}
