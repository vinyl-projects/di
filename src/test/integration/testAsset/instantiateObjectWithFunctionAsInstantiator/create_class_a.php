<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithFunctionAsInstantiator;

function create_class_a(string $message): ClassA
{
    return new ClassA($message);
}
