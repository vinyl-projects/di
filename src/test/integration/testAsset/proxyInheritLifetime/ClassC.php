<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\proxyInheritLifetime;

/**
 * Class ClassC
 */
class ClassC
{
    public ClassB $first;
    public ClassB $second;

    public function __construct(ClassB $first, ClassB $second)
    {
        $this->first = $first;
        $this->second = $second;
    }
}
