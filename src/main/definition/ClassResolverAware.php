<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface ClassResolverAware
 */
interface ClassResolverAware
{
    public function injectClassResolver(ClassResolver $resolver): void;
}
