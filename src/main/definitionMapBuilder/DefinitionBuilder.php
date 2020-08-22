<?php

declare(strict_types=1);

namespace vinyl\di\definitionMapBuilder;

use vinyl\di\Definition;
use vinyl\di\definition\DefinitionMap;
use vinyl\di\definition\Lifetime;
use vinyl\di\definition\Instantiator;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\definitionMapBuilder\definitionBuilder\Arguments;
use vinyl\std\lang\ClassObject;

/**
 * Class DefinitionBuilder
 */
class DefinitionBuilder
{
    private DefinitionMapBuilder $definitionMapBuilder;
    private ?Definition $definition = null;
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
        assert($this->definition !== null);
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
        assert($this->definition !== null);
        $this->definition->changeLifetime($lifetime);

        return $this;
    }

    public function changeInstantiator(Instantiator $instantiator): self
    {
        assert($this->definition !== null);
        $this->definition->changeInstantiator($instantiator);

        return $this;
    }

    public function replaceClass(string $newClass): self
    {
        assert($this->definition !== null);
        $this->definition->replaceClass(ClassObject::create($newClass));

        return $this;
    }

    public function inheritArguments(bool $status): self
    {
        assert($this->definition !== null);
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
