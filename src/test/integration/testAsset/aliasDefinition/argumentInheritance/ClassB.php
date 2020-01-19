<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\aliasDefinition\argumentInheritance;

use stdClass;

class ClassB extends ClassA
{
    public $param3;

    public function __construct(string $param1, string $param3, stdClass $param2)
    {
        parent::__construct($param1, $param2);
        $this->param3 = $param3;
    }
}
