<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Vector;

/**
 * Class ParentClassesProvider
 */
class ParentClassesProvider
{
    /**
     * Returns parent classes
     *
     * @return \vinyl\std\lang\collections\Vector<ClassObject>
     */
    public function find(ClassObject $classObject): Vector
    {
        return $classObject->toParentClassObjectVector();
    }
}
