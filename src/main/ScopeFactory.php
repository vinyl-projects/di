<?php

declare(strict_types=1);

namespace vinyl\di;

use Psr\Container\ContainerInterface;

/**
 * Interface ScopeFactory
 */
interface ScopeFactory
{
    /**
     * Returns new scoped container
     *
     * @return \Psr\Container\ContainerInterface
     */
    public function createScopedContainer(): ContainerInterface;
}
