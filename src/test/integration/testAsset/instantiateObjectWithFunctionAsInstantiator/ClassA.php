<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithFunctionAsInstantiator;

/**
 * Class ClassA
 */
class ClassA
{
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
