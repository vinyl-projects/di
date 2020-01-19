<?php
declare(strict_types=1);


namespace vinyl\diTest\integration\testAsset\optionalInterfaceUsedAsArgument;


class ClassA
{
    public ?InterfaceB $interfaceB;

    public function __construct(?InterfaceB $interfaceB = null)
    {
        $this->interfaceB = $interfaceB;
    }
}
