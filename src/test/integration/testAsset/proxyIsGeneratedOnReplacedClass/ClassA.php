<?php

declare(strict_types=1);


namespace vinyl\diTest\integration\testAsset\proxyIsGeneratedOnReplacedClass;


class ClassA
{
    public ClassB $classB;

    /**
     * ClassA constructor.
     */
    public function __construct(ClassB $classB)
    {
        $this->classB = $classB;
    }
}
