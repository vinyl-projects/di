<?php

declare(strict_types=1);

namespace vinyl\di;

use InvalidArgumentException;
use RuntimeException;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\InterfaceObject;
use function interface_exists;
use function is_a;

/**
 * Class InterfaceImplementationDefinition
 */
final class InterfaceImplementationDefinition extends AbstractDefinition implements RootDefinition
{
    private ClassObject $classObject;

    public function __construct(InterfaceObject $interface, ClassObject $class)
    {
        $this->classObject = $class;

        parent::__construct($interface->name());

        if (!is_a($class->name(), $interface->name(), true)) {
            throw new InvalidArgumentException("Class [{$class->name()}] must be an instance of [{$interface}].");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function classObject(): ClassObject
    {
        return $this->classObject;
    }

    /**
     * {@inheritDoc}
     */
    public function replaceClass(ClassObject $newCLass): ClassObject
    {
        $oldClass = $this->classObject;
        $this->classObject = $newCLass;

        return $oldClass;
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
