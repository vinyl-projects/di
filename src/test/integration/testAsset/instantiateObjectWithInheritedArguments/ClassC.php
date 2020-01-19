<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithInheritedArguments;

class ClassC extends ClassD
{
    public string $surname;

    public function __construct(string $surname, string $name, string $nickname, string $street)
    {
        parent::__construct($name, $nickname, $street);
        $this->surname = $surname;
    }
}
