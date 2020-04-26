<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\std\lang\ClassObject;

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
