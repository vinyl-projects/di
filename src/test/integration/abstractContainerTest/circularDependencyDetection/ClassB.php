<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\abstractContainerTest\circularDependencyDetection;

class ClassB
{
    public $classC;

    public function __construct(ClassC $classC)
    {
        $this->classC = $classC;
    }
}
