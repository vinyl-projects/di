<?php

declare(strict_types=1);

namespace vinyl\di\definitionMapBuilder\definitionBuilder\arguments;

use vinyl\di\ClassDefinition;
use vinyl\di\definition\arrayValue\OrderableBoolValue;
use vinyl\di\definition\arrayValue\OrderableFloatValue;
use vinyl\di\definition\arrayValue\OrderableIntValue;
use vinyl\di\definition\arrayValue\OrderableObjectValue;
use vinyl\di\definition\arrayValue\OrderableProxyValue;
use vinyl\di\definition\arrayValue\OrderableStringValue;
use vinyl\di\definition\DefinitionMap;
use vinyl\di\definition\value\ArrayListValue;
use vinyl\di\definitionMapBuilder\definitionBuilder\Arguments;
use vinyl\std\lang\ClassObject;

/**
 * Class ListConfigurator
 */
final class ListConfigurator
{
    private Arguments $parent;
    private ArrayListValue $value;
    private DefinitionMap $definitionMap;

    public function __construct(Arguments $parent, DefinitionMap $definitionMap)
    {
        $this->parent = $parent;
        $this->definitionMap = $definitionMap;
    }

    public function intItem(?int $value, ?int $sortOrder = null): self
    {
        $this->value->add(new OrderableIntValue($value, $sortOrder));

        return $this;
    }

    public function stringItem(?string $value, ?int $sortOrder = null): self
    {
        $this->value->add(new OrderableStringValue($value, $sortOrder));

        return $this;
    }

    public function boolItem(?bool $value, ?int $sortOrder = null): self
    {
        $this->value->add(new OrderableBoolValue($value, $sortOrder));

        return $this;
    }

    public function floatItem(?float $value, ?int $sortOrder = null): self
    {
        $this->value->add(new OrderableFloatValue($value, $sortOrder));

        return $this;
    }

    public function classItem(string $className, ?int $sortOrder = null): self
    {
        if (!$this->definitionMap->contains($className)) {
            $this->definitionMap->insert(new ClassDefinition(ClassObject::create($className)));
        }

        $this->value->add(new OrderableStringValue($className, $sortOrder));
        return $this;
    }

    public function objectItem(string $definitionId, ?int $sortOrder = null): self
    {
        $this->value->add(new OrderableObjectValue($definitionId, $sortOrder));

        return $this;
    }

    public function proxyItem(string $definitionId, ?int $sortOrder = null): self
    {
        $this->value->add(new OrderableProxyValue($definitionId, $sortOrder));

        return $this;
    }

    public function end(): Arguments
    {
        return $this->parent;
    }

    public function _updateValue(ArrayListValue $currentValue): void
    {
        $this->value = $currentValue;
    }
}
