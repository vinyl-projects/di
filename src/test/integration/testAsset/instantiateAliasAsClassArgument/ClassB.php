<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateAliasAsClassArgument;


class ClassB
{
    public ClassA $classA;

    public function __construct(ClassA $classA)
    {
        $this->classA = $classA;
    }
}
