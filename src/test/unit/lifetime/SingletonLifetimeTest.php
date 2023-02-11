<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\lifetime;

use vinyl\di\definition\Lifetime;
use vinyl\di\definition\SingletonLifetime;

/**
 * Class SingletonLifetimeTest
 */
class SingletonLifetimeTest extends LifetimeTestCase
{
    protected function createLifetime(): Lifetime
    {
        return SingletonLifetime::get();
    }
}
