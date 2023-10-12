<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory\testAsset\instantiatePrototypeObjectThatDependsOnClassWithPrivateConstructor;

class ClassA
{
    public function __construct(public ClassB $classB)
    {
    }
}
