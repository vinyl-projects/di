<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithInheritedArguments;

class ClassB extends ClassC
{
    public int $age;

    public function __construct(int $age, string $surname, string $name, string $nickname, string $street)
    {
        parent::__construct($surname, $name, $nickname, $street);
        $this->age = $age;
    }
}
