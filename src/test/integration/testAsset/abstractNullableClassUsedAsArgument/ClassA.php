<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\abstractNullableClassUsedAsArgument;

class ClassA
{
    public ?ClassB $classB;

    /**
     * ClassA constructor.
     */
    public function __construct(?ClassB $classB = null)
    {
        $this->classB = $classB;
    }
}
