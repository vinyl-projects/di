<?php

declare(strict_types=1);

namespace vinyl\di;

/**
 * Class ClassDefinition
 */
final class ClassDefinition extends AbstractDefinition
{
    /**
     * ClassDefinition constructor.
     */
    public function __construct(string $class)
    {
        parent::__construct($class, $class);
    }
}
