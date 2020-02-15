<?php

declare(strict_types=1);

namespace vinyl\di\factory;

use function assert;
use function is_array;

/**
 * Class ParentClassesProvider
 */
class ParentClassesProvider
{
    /**
     * Returns parent classes
     *
     * @return string[]
     */
    public function find(string $className): array
    {
        $classParents = class_parents($className);

        assert(is_array($classParents));

        return $classParents;
    }
}
