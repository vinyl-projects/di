<?php

declare(strict_types=1);


namespace vinyl\diTest\integration\testAsset\proxiedObjectInjectsItself;


class ClassA
{
    public ClassA $classA;

    public function __construct(ClassA $classA)
    {
        $this->classA = $classA;
    }
}
