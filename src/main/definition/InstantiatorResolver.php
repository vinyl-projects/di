<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;
use vinyl\std\lang\collections\Map;

/**
 * Interface InstantiatorResolver
 */
interface InstantiatorResolver
{
    /**
     * Returns resolved {@see Instantiator} for given definition
     *
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     */
    public function resolve(Definition $definition, Map $definitionMap): Instantiator;
}
