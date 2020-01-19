<?php

declare(strict_types=1);

namespace vinyl\di\proxy;

/**
 * Class ProxyGeneratorResult
 */
final class ProxyGeneratorResult
{
    public string $className;
    public string $classContent;

    /**
     * ProxyGeneratorResult constructor.
     */
    public function __construct(string $className, string $classContent)
    {
        $this->className = $className;
        $this->classContent = $classContent;
    }
}
