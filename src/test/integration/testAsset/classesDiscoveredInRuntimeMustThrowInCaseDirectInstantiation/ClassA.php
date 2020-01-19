<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation;

class ClassA
{
    public ClassB $classB;
    public object $otherObject;
    public ClassD $proxy;

    public function __construct(ClassB $classB, object $otherObject, ClassD $proxy)
    {
        $this->classB = $classB;
        $this->otherObject = $otherObject;
        $this->proxy = $proxy;
    }
}
