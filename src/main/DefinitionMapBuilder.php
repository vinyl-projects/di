<?php

declare(strict_types=1);

namespace vinyl\di;

use LogicException;
use OutOfBoundsException;
use vinyl\di\definitionMapBuilder\DefinitionBuilder;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;
use vinyl\std\lang\collections\MutableMap;
use function vinyl\std\lang\collections\mutableMapOf;

/**
 * Class DefinitionMapBuilder
 */
class DefinitionMapBuilder
{
    /** @var \vinyl\std\lang\collections\MutableMap<string, \vinyl\di\Definition> */
    private MutableMap $definitionMap;
    private DefinitionBuilder $definitionBuilder;

    /**
     * containerBuilder constructor.
     */
    public function __construct()
    {
        $this->definitionMap = mutableMapOf();
        $this->definitionBuilder = new DefinitionBuilder($this, $this->definitionMap);
    }

    /**
     * Registers default interface implementation
     */
    public function interfaceImplementation(string $interface, string $className): self
    {
        $interfaceImplementationDefinition = new InterfaceImplementationDefinition($interface, ClassObject::create($className));
        $this->definitionMap->put($interfaceImplementationDefinition->id(), $interfaceImplementationDefinition);

        return $this;
    }

    public function classDefinition(string $class): DefinitionBuilder
    {
        $typeDefinition = $this->definitionMap->find($class);

        if ($typeDefinition !== null) {
            throw new LogicException("Definition with id [{$class}] already defined.");
        }

        $typeDefinition = new ClassDefinition(ClassObject::create($class));
        $this->definitionMap->put($typeDefinition->id(), $typeDefinition);

        $this->definitionBuilder->_updateDefinition($typeDefinition);

        return $this->definitionBuilder;
    }

    public function alias(string $definitionId, string $class): DefinitionBuilder
    {
        $typeDefinition = $this->definitionMap->find($definitionId);

        if ($typeDefinition !== null) {
            throw new LogicException("Definition with id [{$definitionId}] already defined.");
        }

        $typeDefinition = new ClassAliasDefinition($definitionId, ClassObject::create($class));
        $this->definitionMap->put($typeDefinition->id(), $typeDefinition);

        $this->definitionBuilder->_updateDefinition($typeDefinition);

        return $this->definitionBuilder;
    }

    public function aliasOnAlias(string $definitionId, string $parentId): DefinitionBuilder
    {
        $definition = $this->definitionMap->find($definitionId);
        if ($definition !== null) {
            throw new LogicException("Definition with id [{$definitionId}] already defined.");
        }

        $definition = new AliasOnAliasDefinition($definitionId, $parentId);
        $this->definitionMap->put($definition->id(), $definition);

        $this->definitionBuilder->_updateDefinition($definition);

        return $this->definitionBuilder;
    }

    public function referenceDefinition(string $definitionId): DefinitionBuilder
    {
        //TODO how to change parent id for AlisOnAlias definition

        $definition = $this->definitionMap->find($definitionId);
        if ($definition === null) {
            throw new OutOfBoundsException("Definition with id [{$definitionId}] is not registered.");
        }

        $this->definitionBuilder->_updateDefinition($definition);

        return $this->definitionBuilder;
    }

    /**
     * @return \vinyl\std\lang\collections\Map<string, \vinyl\di\Definition>
     */
    public function build(): Map
    {
        $definitionMap = $this->definitionMap;
        $this->definitionMap = mutableMapOf();

        return $definitionMap;
    }
}
