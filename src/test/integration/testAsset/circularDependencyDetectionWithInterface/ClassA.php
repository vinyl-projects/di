<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\circularDependencyDetectionWithInterface;

class ClassA implements ClassAInterface
{
    public ClassAInterface $classA;

    public function __construct(ClassAInterface $classA)
    {
        $this->classA = $classA;
    }
}
