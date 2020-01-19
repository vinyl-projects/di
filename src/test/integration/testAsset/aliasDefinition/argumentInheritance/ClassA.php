<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\aliasDefinition\argumentInheritance;

use stdClass;

class ClassA
{
    public $param1;
    public $param2;

    public function __construct(string $param1, stdClass $param2)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
    }
}
