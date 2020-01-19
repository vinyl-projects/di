<?php

declare(strict_types=1);

namespace vinyl\di\definitionMapBuilder;

use vinyl\di\Definition;
use vinyl\di\definition\DefinitionMap;
use vinyl\di\definition\Lifetime;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\definitionMapBuilder\definitionBuilder\Arguments;

/**
 * Class DefinitionBuilder
 */
class DefinitionBuilder
{
    private DefinitionMapBuilder $definitionMapBuilder;
    private Definition $definition;
    private DefinitionMap $definitionMap;

    /**
     * DefinitionBuilder constructor.
     */
    public function __construct(DefinitionMapBuilder $containerBuilder, DefinitionMap $definitionMap)
    {
        $this->definitionMapBuilder = $containerBuilder;
        $this->definitionMap = $definitionMap;
    }

    /**
     * Starts arguments building
     */
    public function arguments(): Arguments
    {
        #todo make arguments reusable
        return new Arguments($this, $this->definition, $this->definitionMap);
    }

    /**
     * Finishes building of the type
     */
    public function end(): DefinitionMapBuilder
    {
        return $this->definitionMapBuilder;
    }

    /**
     * Set new definition lifetime
     */
    public function lifetime(Lifetime $lifetime): self
    {
        $this->definition->changeLifetime($lifetime);

        return $this;
    }

    public function changeConstructorMethod(string $methodName): self
    {
        $this->definition->changeConstructorMethod($methodName);

        return $this;
    }

    public function replaceClass(string $newClass): self
    {
        $this->definition->replaceClass($newClass);

        return $this;
    }

    public function inheritArguments(bool $status): self
    {
        $this->definition->toggleArgumentInheritance($status);

        return $this;
    }

    /**
     * @internal
     */
    public function _updateDefinition(Definition $definition): void
    {
        $this->definition = $definition;
    }
}
