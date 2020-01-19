<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\automaticRegisterClassArgument;

class ClassA
{
    public string $className;
    public array $classList;
    public array $classMap;

    public function __construct(string $className, array $classList, array $classMap)
    {
        $this->className = $className;
        $this->classList = $classList;
        $this->classMap = $classMap;
    }
}
