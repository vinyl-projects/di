<?php

declare(strict_types=1);

namespace vinyl\di\proxy;

use vinyl\std\ClassObject;

/**
 * Interface ProxyGenerator
 */
interface ProxyGenerator
{
    public const PROXY_ARGUMENT_NAME = 'definitionId';

    /**
     * Generates proxy for provided class
     *
     * @return \vinyl\di\proxy\ProxyGeneratorResult
     * @throws \vinyl\di\proxy\ProxyGeneratorException
     */
    public function generate(ClassObject $class): ProxyGeneratorResult;
}
