<?php

declare(strict_types=1);

namespace vinyl\di;

use LogicException;
use OutOfBoundsException;
use vinyl\di\definition\DefinitionMap;
use vinyl\di\definitionMapBuilder\DefinitionBuilder;
use vinyl\std\lang\ClassObject;

/**
 * Class DefinitionMapBuilder
 */
class DefinitionMapBuilder
{
    private DefinitionMap $definitionMap;
    private DefinitionBuilder $definitionBuilder;

    /**
     * containerBuilder constructor.
     */
    public function __construct()
    {
        $this->definitionMap = new DefinitionMap([]);
        $this->definitionBuilder = new DefinitionBuilder($this, $this->definitionMap);
    }

    /**
     * Registers default interface implementation
     */
    public function interfaceImplementation(string $interface, string $className): self
    {
        $this->definitionMap->insert(new InterfaceImplementationDefinition($interface, ClassObject::create($className)));

        return $this;
    }

    public function classDefinition(string $class): DefinitionBuilder
    {
        $typeDefinition = $this->definitionMap->find($class);

        if ($typeDefinition !== null) {
            throw new LogicException("Definition with id [{$class}] already defined.");
        }

        $typeDefinition = new ClassDefinition(ClassObject::create($class));
        $this->definitionMap->insert($typeDefinition);

        $this->definitionBuilder->_updateDefinition($typeDefinition);

        return $this->definitionBuilder;
    }

    public function alias(string $definitionId, string $class): DefinitionBuilder
    {
        $typeDefinition = $this->definitionMap->find($definitionId);

        if ($typeDefinition !== null) {
            throw new LogicException("Definition with id [{$definitionId}] already defined.");
        }

        $typeDefinition = new AliasDefinition($definitionId, ClassObject::create($class));
        $this->definitionMap->insert($typeDefinition);

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
        $this->definitionMap->insert($definition);

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

    public function build(): DefinitionMap
    {
        $definitionMap = clone $this->definitionMap;
        $this->definitionMap = new DefinitionMap([]);

        return $definitionMap;
    }
}
