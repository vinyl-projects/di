<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory\testAsset\instantiateObjectWithProvidedArguments;

use stdClass;

class ClassA
{
    public string $param1;
    public int $param2;
    public array $param3;
    public stdClass $param4;

    public function __construct(string $param1, int $param2, array $param3, stdClass $param4)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
        $this->param4 = $param4;
    }
}
