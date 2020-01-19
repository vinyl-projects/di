<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;

/**
 * Interface ValueCollector
 */
interface ValueCollector
{
    /**
     * Collects and merge all available values for type (parents values could be included) defined in configuration
     */
    public function collect(Definition $definition, UnmodifiableDefinitionMap $definitionMap): ValueMap;
}
