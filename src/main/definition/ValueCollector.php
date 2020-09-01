<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;
use vinyl\std\lang\collections\Map;

/**
 * Interface ValueCollector
 */
interface ValueCollector
{
    /**
     * Collects and merge all available values for type (parents values could be included) defined in configuration
     *
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     */
    public function collect(Definition $definition, Map $definitionMap): ValueMap;
}
