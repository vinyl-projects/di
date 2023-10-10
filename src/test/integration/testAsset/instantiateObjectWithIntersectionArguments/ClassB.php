<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithIntersectionArguments;

class ClassB
{
    public function __construct(
        public \Countable&\IteratorAggregate        $param1,
        public null|(\Countable&\IteratorAggregate) $param2,
        public null|(\Countable&\IteratorAggregate) $param3 = null,
        public null|(\Countable&\IteratorAggregate) $param4 = null,
        public null|(\Countable&\IteratorAggregate)|array $param5 = null,
    )
    {
    }
}