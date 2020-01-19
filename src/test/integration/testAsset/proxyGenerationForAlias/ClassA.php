<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\proxyGenerationForAlias;

class ClassA
{
    private $classB;

    public function __construct(ClassB $classB)
    {
        $this->classB = $classB;
    }

    public function classB(): ClassB
    {
        return $this->classB;
    }
}
