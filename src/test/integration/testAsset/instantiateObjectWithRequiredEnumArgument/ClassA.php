<?php

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithRequiredEnumArgument;

class ClassA
{
    public function __construct(public EnumArgument $argument, public string $argument2)
    {
    }
}
