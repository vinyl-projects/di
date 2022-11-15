<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\std\lang\ClassObject;

/**
 * Class RuntimeClassDefinition
 *
 * Definition to mark classes that must be injected to DI after instantiation
 */
final class RuntimeClassDefinition extends AbstractDefinition
{
    /**
     * RuntimeClassDefinition constructor.
     */
    public function __construct(ClassObject $class)
    {
        parent::__construct($class->name(), $class);
    }
}
