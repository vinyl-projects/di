<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\aliasUsedAsArgument;

class ClassA
{
    public ClassB $classB;

    public function __construct(ClassB $classB)
    {
        $this->classB = $classB;
    }
}
