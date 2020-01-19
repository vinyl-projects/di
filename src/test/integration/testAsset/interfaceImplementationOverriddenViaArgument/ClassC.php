<?php

declare(strict_types=1);


namespace vinyl\diTest\integration\testAsset\interfaceImplementationOverriddenViaArgument;


class ClassC
{
    public InterfaceA $interfaceA;

    public function __construct(InterfaceA $interfaceA)
    {
        $this->interfaceA = $interfaceA;
    }
}
