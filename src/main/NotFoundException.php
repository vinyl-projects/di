<?php

declare(strict_types=1);

namespace vinyl\di;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class NotFoundException
 */
class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
