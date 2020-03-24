<?php

declare(strict_types=1);

namespace vinyl\di\factory;

use vinyl\std\ClassObject;

/**
 * Class ParentClassesProvider
 */
class ParentClassesProvider
{
    /**
     * Returns parent classes
     *
     * @return ClassObject[]
     */
    public function find(ClassObject $classObject): array
    {
        return $classObject->toParentClassObjectList();
    }
}
