<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithVariadicArgument;

class ClassA
{
    public array $data;

    /**
     * ClassA constructor.
     */
    public function __construct(...$data)
    {
        $this->data = $data;
    }
}
