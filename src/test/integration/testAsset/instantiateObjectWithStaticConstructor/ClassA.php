<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\instantiateObjectWithStaticConstructor;

class ClassA
{
    public string $data;

    /**
     * ClassA constructor.
     */
    private function __construct(string $data)
    {
        $this->data = $data;
    }

    public static function create(string $data): self
    {
        return new self($data);
    }

    public static function createWithoutArguments(): self
    {
        return new self('without arguments');
    }
}
