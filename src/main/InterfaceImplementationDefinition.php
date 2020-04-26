<?php

declare(strict_types=1);

namespace vinyl\di;

use InvalidArgumentException;
use RuntimeException;
use vinyl\std\lang\ClassObject;
use function interface_exists;
use function is_a;

/**
 * Class InterfaceImplementationDefinition
 *
 * @todo we must be able to set alias as implementation
 */
final class InterfaceImplementationDefinition extends AbstractDefinition
{
    public function __construct(string $interface, ClassObject $class)
    {
        if (!interface_exists($interface)) {
            throw new InvalidArgumentException("Interface {$interface} not exists.");
        }

        parent::__construct($interface, $class);

        if (!is_a($class->className(), $interface, true)) {
            throw new InvalidArgumentException("Class [{$class->className()}] must be an instance of [{$interface}].");
        }
    }

    /**
     * {@inheritDoc}
     *
     * Will always return <b>true</b> for this definition type
     */
    public function isArgumentInheritanceEnabled(): bool
    {
        return true;
    }

    /**
     * Unsupported for this definition type. Will always throw {@see RuntimeException} exception
     */
    public function toggleArgumentInheritance(bool $status): ?bool
    {
        throw new RuntimeException('It is impossible to change inheritance status for interface implementation definition. It is always enabled by default.');
    }
}
