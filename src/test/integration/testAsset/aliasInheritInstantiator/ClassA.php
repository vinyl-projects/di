<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\aliasInheritInstantiator;

/**
 * Class ClassA
 */
class ClassA
{
    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }
}
