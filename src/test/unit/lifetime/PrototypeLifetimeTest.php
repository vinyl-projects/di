<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\lifetime;

use vinyl\di\definition\Lifetime;
use vinyl\di\definition\PrototypeLifetime;

/**
 * Class PrototypeLifetimeTest
 */
class PrototypeLifetimeTest extends LifetimeTestCase
{
    protected function createLifetime(): Lifetime
    {
        return PrototypeLifetime::get();
    }
}
