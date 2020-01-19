<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\aliasDefinition\argumentInheritance;

use stdClass;

class ClassC extends ClassB
{
    public $param4;

    public function __construct(
        $param1,
        $param3,
        stdClass $param2,
        $param4
    ) {
        parent::__construct($param1, $param3, $param2);
        $this->param4 = $param4;
    }
}
