<?php

declare(strict_types=1);

namespace vinyl\di\factory;

use function vinyl\di\class_find_parents;

/**
 * Class ParentClassesProvider
 */
class ParentClassesProvider
{
    /**
     *
     *
     * @param string $className
     *
     * @return string[]
     */
    public function find(string $className): array
    {
        return class_find_parents($className);
    }
}
