<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\std\lang\ClassObject;

interface RootDefinition
{
    /**
     * Returns entry object
     */
    public function classObject(): ClassObject;

    /**
     * Replaces the class for current definition instance
     *
     * @return ClassObject Previous class object
     */
    public function replaceClass(ClassObject $newCLass): ClassObject;
}
