<?php

declare(strict_types=1);


namespace vinyl\diTest\integration\testAsset\allDependenciesArePrototype;


class ClassB
{
    public ClassC $classC;

    public function __construct(ClassC $classC)
    {
        $this->classC = $classC;
    }
}
