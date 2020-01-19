<?php

declare(strict_types=1);

namespace vinyl\di;

use Psr\Container\ContainerInterface;

/**
 * Interface ContainerAware
 *
 * @internal
 */
interface ContainerAware
{
    public function injectContainer(ContainerInterface $container): void;
}
