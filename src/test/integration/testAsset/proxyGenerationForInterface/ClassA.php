<?php
declare(strict_types=1);


namespace vinyl\diTest\integration\testAsset\proxyGenerationForInterface;

class ClassA
{
    public InterfaceB $interfaceB;

    public function __construct(InterfaceB $interfaceB)
    {
        $this->interfaceB = $interfaceB;
    }
}
