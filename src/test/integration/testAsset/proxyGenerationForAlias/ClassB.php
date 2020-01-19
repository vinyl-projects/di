<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\proxyGenerationForAlias;

class ClassB
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
