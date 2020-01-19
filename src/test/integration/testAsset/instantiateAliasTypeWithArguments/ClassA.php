<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateAliasTypeWithArguments;

use stdClass;

class ClassA
{
    public int $intArg;
    public float $floatArg;
    public string $stringArg;
    public bool $boolArg;
    public stdClass $objectArg;
    public int $intArgOptional;
    public float $floatArgOptional;
    public string $stringArgOptional;
    public bool $boolArgOptional;
    public stdClass $objectArgOptional;

    public function __construct(
        int $intArg,
        float $floatArg,
        string $stringArg,
        bool $boolArg,
        stdClass $objectArg,
        int $intArgOptional = 0,
        float $floatArgOptional = 0.0,
        string $stringArgOptional = '',
        bool $boolArgOptional = false,
        stdClass $objectArgOptional = null

    ) {
        $this->intArg = $intArg;
        $this->floatArg = $floatArg;
        $this->stringArg = $stringArg;
        $this->boolArg = $boolArg;
        $this->objectArg = $objectArg;
        $this->intArgOptional = $intArgOptional;
        $this->floatArgOptional = $floatArgOptional;
        $this->stringArgOptional = $stringArgOptional;
        $this->boolArgOptional = $boolArgOptional;
        $this->objectArgOptional = $objectArgOptional;
    }
}
