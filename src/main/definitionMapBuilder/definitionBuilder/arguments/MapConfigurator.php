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
use vinyl\di\definition\value\ArrayMapValue;
use vinyl\di\definitionMapBuilder\definitionBuilder\Arguments;
use vinyl\std\lang\ClassObject;
use function assert;

/**
 * Class MapConfigurator
 */
final class MapConfigurator
{
    private Arguments $parent;
    private ?ArrayMapValue $mapValue = null;
    private DefinitionMap $definitionMap;

    /**
     * MapConfigurator constructor.
     */
    public function __construct(Arguments $parent, DefinitionMap $definitionMap)
    {
        $this->parent = $parent;
        $this->definitionMap = $definitionMap;
    }

    /**
     * @param string|int $key
     */
    public function classItem($key, string $className, ?int $order = null): self
    {
        assert($this->mapValue !== null);

        if (!$this->definitionMap->contains($className)) {
            $this->definitionMap->insert(new ClassDefinition(ClassObject::create($className)));
        }

        $this->mapValue->put($key, new OrderableStringValue($className, $order));

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function objectItem($key, string $definitionId, ?int $order = null): self
    {
        assert($this->mapValue !== null);
        $itemValue = new OrderableObjectValue($definitionId, $order);
        $this->mapValue->put($key, $itemValue);

        return $this;
    }

    /**
     * @param int|string $key
     */
    public function proxyItem($key, ?string $value, ?int $order = null): self
    {
        assert($this->mapValue !== null);

        $itemValue = new OrderableProxyValue($value, $order);
        $this->mapValue->put($key, $itemValue);

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function intItem($key, ?int $value, ?int $order = null): self
    {
        assert($this->mapValue !== null);
        $this->mapValue->put($key, new OrderableIntValue($value, $order));

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function floatItem($key, ?float $value, ?int $order = null): self
    {
        assert($this->mapValue !== null);
        $this->mapValue->put($key, new OrderableFloatValue($value, $order));

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function boolItem($key, ?bool $value, ?int $order = null): self
    {
        assert($this->mapValue !== null);
        $this->mapValue->put($key, new OrderableBoolValue($value, $order));

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function stringItem($key, ?string $value, ?int $order = null): self
    {
        assert($this->mapValue !== null);
        $this->mapValue->put($key, new OrderableStringValue($value, $order));

        return $this;
    }

    public function end(): Arguments
    {
        $this->mapValue = null;

        return $this->parent;
    }

    /**
     * @internal
     */
    public function _updateValue(ArrayMapValue $mapValue): void
    {
        $this->mapValue = $mapValue;
    }
}
