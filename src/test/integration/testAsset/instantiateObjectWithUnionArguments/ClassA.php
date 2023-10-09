<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithUnionArguments;

class ClassA
{
    public function __construct(public null|int|string|EnumArgument $argument)
    {
    }
}
