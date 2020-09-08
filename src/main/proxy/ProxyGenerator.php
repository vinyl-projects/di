<?php

declare(strict_types=1);

namespace vinyl\di\proxy;

use vinyl\std\lang\ClassObject;

/**
 * Interface ProxyGenerator
 */
interface ProxyGenerator
{
    public const PROXY_ARGUMENT_NAME = 'definitionId';

    /**
     * Generates proxy content for given class with given proxy class name
     *
     * @throws \vinyl\di\proxy\ProxyGeneratorException
     */
    public function generate(ClassObject $class, string $proxyClassName): string;
}
