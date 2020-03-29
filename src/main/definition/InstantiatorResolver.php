<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;

/**
 * Interface InstantiatorResolver
 */
interface InstantiatorResolver
{
    /**
     * Returns resolved {@see Instantiator} for given definition
     */
    public function resolve(Definition $definition, UnmodifiableDefinitionMap $definitionMap): Instantiator;
}
