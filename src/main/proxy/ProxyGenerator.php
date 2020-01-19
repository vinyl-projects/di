<?php

declare(strict_types=1);

namespace vinyl\di\proxy;

/**
 * Interface ProxyGenerator
 */
interface ProxyGenerator
{
    public const PROXY_ARGUMENT_NAME = 'definitionId';

    /**
     * Generates proxy for provided class
     *
     * @param string $class
     *
     * @throws \vinyl\di\proxy\ProxyGeneratorException
     */
    public function generate(string $class): ProxyGeneratorResult;
}
