<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\std\lang\ClassObject;

/**
 * Class ClassDefinition
 */
final class ClassDefinition extends AbstractDefinition implements RootDefinition
{
    private ClassObject $classObject;

    /**
     * ClassDefinition constructor.
     */
    public function __construct(ClassObject $class)
    {
        $this->classObject  = $class;
        parent::__construct($class->name());
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
}
