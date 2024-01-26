<?php

declare(strict_types=1);

namespace vinyl\di;

use LogicException;
use OutOfBoundsException;
use vinyl\di\definitionMapBuilder\DefinitionBuilder;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;
use vinyl\std\lang\collections\MutableMap;
use vinyl\std\lang\InterfaceObject;
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
        $interfaceImplementationDefinition = new InterfaceImplementationDefinition(
            InterfaceObject::create($interface),
            ClassObject::create($className)
        );

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

    /**
     * @param class-string|interface-string $entry
     *
     * @return void
     */
    public function runtimeClassDefinition(string $entry): void
    {
        $definition = $this->definitionMap->find($entry);

        if ($definition !== null) {
            //TODO log it ?
        }

        $definition = new RuntimeDefinition(ClassObject::create($entry));
        $this->definitionMap->put($definition->id(), $definition);

        $this->definitionBuilder->_updateDefinition($definition);

        return;
    }

    public function runtimeInterfaceDefinition(string $entry): void
    {
        $definition = $this->definitionMap->find($entry);

        if ($definition !== null) {
            //TODO log it ?
        }

        $definition = new RuntimeDefinition(InterfaceObject::create($entry));
        $this->definitionMap->put($definition->id(), $definition);

        $this->definitionBuilder->_updateDefinition($definition);

        return;
    }

    public function virtualType(string $virtualTypeId, string $parentId): DefinitionBuilder
    {
        $typeDefinition = $this->definitionMap->find($virtualTypeId);

        if ($typeDefinition !== null) {
            throw new LogicException("Virtual type with id [{$virtualTypeId}] already defined.");
        }

        $typeDefinition = VirtualTypeDefinition::new($virtualTypeId, $parentId);
        $this->definitionMap->put($typeDefinition->id(), $typeDefinition);

        $this->definitionBuilder->_updateDefinition($typeDefinition);

        return $this->definitionBuilder;
    }

    public function referenceDefinition(string $definitionId): DefinitionBuilder
    {
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
