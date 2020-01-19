<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\abstractContainerTest\circularDependencyDetection;

class ClassA
{
    public $classB;

    public function __construct(ClassB $classB)
    {
        $this->classB = $classB;
    }
}
