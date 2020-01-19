<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use Iterator;
use vinyl\di\Definition;
use function array_key_exists;

/**
 * Class DefinitionToDependencyMap
 *
 * @implements Iterator<\vinyl\di\Definition, bool>
 */
final class DefinitionToDependencyMap implements Iterator
{
    private int $currentElement = 0;

    /** @var array<string, bool> */
    private array $idToDependencyMap = [];

    /** @var \vinyl\di\Definition[] */
    private array $definitionList = [];

    public function insert(Definition $definition, bool $isDependency): void
    {
        $this->definitionList[] = $definition;
        $this->idToDependencyMap[$definition->id()] = $isDependency;
    }

    public function add(DefinitionToDependencyMap $dependencyMap): void
    {
        foreach ($dependencyMap as $definition => $isDependency) {
            $this->insert($definition, $isDependency);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        ++$this->currentElement;
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        return array_key_exists($this->currentElement, $this->definitionList);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        $this->currentElement = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function current(): bool
    {
        $definition = $this->definitionList[$this->currentElement];

        return $this->idToDependencyMap[$definition->id()];
    }

    /**
     * {@inheritDoc}
     */
    public function key(): Definition
    {
        return $this->definitionList[$this->currentElement];
    }
}
