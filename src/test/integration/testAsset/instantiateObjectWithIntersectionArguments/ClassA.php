<?php

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithIntersectionArguments;

use Traversable;

class ClassA implements \Countable, \IteratorAggregate
{

    public function getIterator(): Traversable
    {
        // TODO: Implement getIterator() method.
    }

    public function count(): int
    {
        // TODO: Implement count() method.
    }
}