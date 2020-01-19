<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateAliasAsClassArgument;


class ClassA
{
    public string $someData;

    public function __construct(string $someData)
    {
        $this->someData = $someData;
    }
}
