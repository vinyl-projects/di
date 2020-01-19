<?php

declare(strict_types=1);


namespace vinyl\diTest\integration\testAsset\interfaceUsedAsArgument;


class ClassB
{
    public InterfaceA $interfaceA;

    public function __construct(InterfaceA $interfaceA)
    {
        $this->interfaceA = $interfaceA;
    }
}
