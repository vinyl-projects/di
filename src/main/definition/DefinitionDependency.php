<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;

/**
 * Class DiscoveredDependency
 */
final class DefinitionDependency
{
    public Definition $definition;
    public bool $isPartOfDependencyGraph;

    /**
     * DiscoveredDependency constructor.
     */
    public function __construct(Definition $definition, bool $isPartOfGraph)
    {
        $this->definition = $definition;
        $this->isPartOfDependencyGraph = $isPartOfGraph;
    }

    /**
     * Creates {@see DefinitionDependency} that must be used as part of graph during transformation in {@see \vinyl\di\definition\DefinitionTransformer}
     */
    public static function create(Definition $definition): self
    {
        return new self($definition, true);
    }

    /**
     * Creates {@see DefinitionDependency} that must be transformed in {@see \vinyl\di\definition\DefinitionTransformer}
     * but should not be part of dependency graph of other {@see Definition}
     */
    public static function createDetachedDependency(Definition $definition): self
    {
        return new self($definition, false);
    }
}
