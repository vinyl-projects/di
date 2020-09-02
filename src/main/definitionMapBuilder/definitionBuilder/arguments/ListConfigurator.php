<?php

declare(strict_types=1);

namespace vinyl\di\definitionMapBuilder\definitionBuilder\arguments;

use vinyl\di\ClassDefinition;
use vinyl\di\definition\value\ArrayListValue;
use vinyl\di\definition\value\BoolValue;
use vinyl\di\definition\value\FloatValue;
use vinyl\di\definition\value\IntValue;
use vinyl\di\definition\value\ObjectValue;
use vinyl\di\definition\value\ProxyValue;
use vinyl\di\definition\value\StringValue;
use vinyl\di\definitionMapBuilder\definitionBuilder\Arguments;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\MutableMap;

/**
 * Class ListConfigurator
 */
final class ListConfigurator
{
    private Arguments $parent;
    private ?ArrayListValue $value = null;
    /** @var \vinyl\std\lang\collections\MutableMap<string, \vinyl\di\Definition> */
    private MutableMap $definitionMap;

    /**
     * ListConfigurator constructor.
     *
     * @param \vinyl\std\lang\collections\MutableMap<string, \vinyl\di\Definition> $definitionMap
     */
    public function __construct(Arguments $parent, MutableMap $definitionMap)
    {
        $this->parent = $parent;
        $this->definitionMap = $definitionMap;
    }

    public function intItem(?int $value): self
    {
        assert($this->value !== null);
        $this->value->add(new IntValue($value));

        return $this;
    }

    public function stringItem(?string $value): self
    {
        assert($this->value !== null);
        $this->value->add(new StringValue($value));

        return $this;
    }

    public function boolItem(?bool $value): self
    {
        assert($this->value !== null);
        $this->value->add(new BoolValue($value));

        return $this;
    }

    public function floatItem(?float $value): self
    {
        assert($this->value !== null);
        $this->value->add(new FloatValue($value));

        return $this;
    }

    public function classItem(string $className): self
    {
        assert($this->value !== null);
        if (!$this->definitionMap->containsKey($className)) {
            $classDefinition = new ClassDefinition(ClassObject::create($className));
            $this->definitionMap->put($classDefinition->id(), $classDefinition);
        }

        $this->value->add(new StringValue($className));

        return $this;
    }

    public function objectItem(string $definitionId): self
    {
        assert($this->value !== null);
        $this->value->add(new ObjectValue($definitionId));

        return $this;
    }

    public function proxyItem(string $definitionId): self
    {
        assert($this->value !== null);
        $this->value->add(new ProxyValue($definitionId));

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
