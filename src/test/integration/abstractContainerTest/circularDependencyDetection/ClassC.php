<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\abstractContainerTest\circularDependencyDetection;

class ClassC
{
    public $classD;

    public function __construct(ClassD $classD)
    {
        $this->classD = $classD;
    }
}
