<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithInheritedArguments;

class ClassD
{
    public string $name;
    public string $nickname;
    public string $street;

    public function __construct(string $name, string $nickname, string $street)
    {
        $this->name = $name;
        $this->nickname = $nickname;
        $this->street = $street;
    }
}
