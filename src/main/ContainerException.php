<?php

declare(strict_types=1);

namespace vinyl\di;

use Exception;
use Psr\Container\ContainerExceptionInterface;

/**
 * Class ContainerException
 */
class ContainerException extends Exception implements ContainerExceptionInterface
{
}
