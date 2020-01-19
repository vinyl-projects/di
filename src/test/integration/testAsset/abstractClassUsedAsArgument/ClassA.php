<?php
declare(strict_types=1);


namespace vinyl\diTest\integration\testAsset\abstractClassUsedAsArgument;


class ClassA
{
    public function __construct(ClassB $classB)
    {
    }
}
