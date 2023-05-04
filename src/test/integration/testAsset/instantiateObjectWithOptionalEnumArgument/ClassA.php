<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithOptionalEnumArgument;

class ClassA
{
    public function __construct(public EnumTestProvider $enumTestProvider = EnumTestProvider::ONE)
    {
    }
}
