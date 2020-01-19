<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\abstractContainerTest\circularDependencyDetection;

class ClassD
{
    public $classA;

    public function __construct(ClassA $classA)
    {
        $this->classA = $classA;
    }
}
