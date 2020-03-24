<?php

declare(strict_types=1);

namespace vinyl\di\definitionMapBuilder\definitionBuilder;

use RuntimeException;
use vinyl\di\ClassDefinition;
use vinyl\di\Definition;
use vinyl\di\definition\DefinitionMap;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\value\ArrayListValue;
use vinyl\di\definition\value\ArrayMapValue;
use vinyl\di\definition\value\BoolValue;
use vinyl\di\definition\value\FloatValue;
use vinyl\di\definition\value\IntValue;
use vinyl\di\definition\value\ObjectValue;
use vinyl\di\definition\value\ProxyValue;
use vinyl\di\definition\value\StringValue;
use vinyl\di\definitionMapBuilder\DefinitionBuilder;
use vinyl\di\definitionMapBuilder\definitionBuilder\arguments\ListConfigurator;
use vinyl\di\definitionMapBuilder\definitionBuilder\arguments\MapConfigurator;
use vinyl\std\ClassObject;

/**
 * Class Arguments
 */
class Arguments
{
    private DefinitionBuilder $parent;
    private Definition $definition;
    private MapConfigurator $mapConfigurator;
    private ListConfigurator $listConfigurator;
    private DefinitionMap $definitionMap;

    /**
     * Arguments constructor.
     */
    public function __construct(
        DefinitionBuilder $parent,
        Definition $definition,
        DefinitionMap $definitionMap
    ) {
        $this->parent = $parent;
        $this->definition = $definition;
        $this->definitionMap = $definitionMap;
        $this->mapConfigurator = new MapConfigurator($this, $this->definitionMap);
        $this->listConfigurator = new ListConfigurator($this, $this->definitionMap);
    }

    public function argument(string $name, DefinitionValue $value): self
    {
        $this->definition->argumentValues()->put($name, $value);

        return $this;
    }

    /**
     * Removes configuration for argument
     */
    public function removeArgument(string $name): self
    {
        $this->definition->argumentValues()->remove($name);

        return $this;
    }

    public function intArgument(string $name, ?int $value): self
    {
        $this->definition->argumentValues()->put($name, new IntValue($value));

        return $this;
    }

    public function floatArgument(string $name, ?float $value): self
    {
        $this->definition->argumentValues()->put($name, new FloatValue($value));

        return $this;
    }

    public function boolArgument(string $name, ?bool $value): self
    {
        $this->definition->argumentValues()->put($name, new BoolValue($value));

        return $this;
    }

    public function stringArgument(string $name, ?string $value): self
    {
        $this->definition->argumentValues()->put($name, new StringValue($value));

        return $this;
    }

    public function arrayMapArgument(string $name): MapConfigurator
    {
        $value = $this->definition->argumentValues()->find($name);

        if ($value === null || !$value instanceof ArrayMapValue) {//TODO log if we override value
            $value = new ArrayMapValue([]);
            $this->definition->argumentValues()->put($name, $value);
        }

        $this->mapConfigurator->_updateValue($value);

        return $this->mapConfigurator;
    }

    public function arrayNullArgument(string $name): self
    {
        $this->definition->argumentValues()->put($name, new ArrayMapValue());

        return $this;
    }

    public function arrayListArgument(string $name): ListConfigurator
    {
        $currentValue = $this->definition->argumentValues()->find($name);

        if ($currentValue === null || !$currentValue instanceof ArrayListValue) {//TODO log if we override value
            $currentValue = new ArrayListValue([]);
            $this->definition->argumentValues()->put($name, $currentValue);
        }

        $this->listConfigurator->_updateValue($currentValue);

        return $this->listConfigurator;
    }

    public function classArgument(string $name, string $className): self
    {
        if (!$this->definitionMap->contains($className)) {
            $this->definitionMap->insert(new ClassDefinition(ClassObject::create($className)));
        }

        $this->definition->argumentValues()->put($name, new StringValue($className));

        return $this;
    }

    public function objectArgument(string $name, ?string $definitionId): self
    {
        $valueHolder = new ObjectValue($definitionId);
        $this->definition->argumentValues()->put($name, $valueHolder);

        return $this;
    }

    public function proxyArgument(string $name, ?string $definitionId): self
    {
        $currentValue = $this->definition->argumentValues()->find($name);

        if ($currentValue === null || $currentValue instanceof ProxyValue) {
            $currentValue = new ProxyValue($definitionId);
            $this->definition->argumentValues()->put($name, $currentValue);

            return $this;
        }

        //TODO
        throw new RuntimeException('Not implemented.');

//        if ($currentValue instanceof TypeDefinition\ObjectValue) {
//            $this->definition->argumentValues()->put($name, new TypeDefinition\ProxyValue($currentValue->value()));
//
//            return $this;
//        }
    }

    /**
     * Ends arguments building
     */
    public function endArguments(): DefinitionBuilder
    {
        return $this->parent;
    }
}
