<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithSortedArrayMap;

class ClassA
{
    public array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }
}
