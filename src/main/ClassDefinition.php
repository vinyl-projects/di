<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\std\ClassObject;

/**
 * Class ClassDefinition
 */
final class ClassDefinition extends AbstractDefinition
{
    /**
     * ClassDefinition constructor.
     */
    public function __construct(ClassObject $class)
    {
        parent::__construct($class->className(), $class);
    }
}
