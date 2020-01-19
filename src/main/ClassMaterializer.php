<?php

declare(strict_types=1);

namespace vinyl\di;

/**
 * Interface ClassMaterializer
 */
interface ClassMaterializer
{
    /**
     * @throws \InvalidArgumentException is thrown if class name or class content is empty
     * @throws \vinyl\di\ClassMaterializerException is thrown if class materialization is impossible
     */
    public function materialize(string $className, string $classContent): void;
}
