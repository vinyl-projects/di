<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\abstractContainerTest\preferenceNotOverrideArguments;

/**
 * Class B
 */
class B implements A
{
    private $name;
    private $surname;

    public function __construct(string $name, string $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function surname(): string
    {
        return $this->surname;
    }
}
