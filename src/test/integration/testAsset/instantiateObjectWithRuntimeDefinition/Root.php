<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithRuntimeDefinition;

class Root
{
    public function __construct(public RootChildRuntime $childRuntime, public RootRuntimeInterface $rootRuntime)
    {
    }
}
