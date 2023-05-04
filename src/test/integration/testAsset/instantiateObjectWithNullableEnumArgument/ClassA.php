<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithNullableEnumArgument;

class ClassA
{
    public function __construct(public ?EnumArgument $argument, public ?EnumArgument $argument2 = null)
    {
    }
}
