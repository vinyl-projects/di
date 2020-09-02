<?php

declare(strict_types=1);

namespace vinyl\di\definitionMapBuilder\definitionBuilder\arguments;

use vinyl\di\ClassDefinition;
use vinyl\di\definition\value\ArrayMapValue;
use vinyl\di\definition\value\BoolValue;
use vinyl\di\definition\value\FloatValue;
use vinyl\di\definition\value\IntValue;
use vinyl\di\definition\value\ObjectValue;
use vinyl\di\definition\value\ProxyValue;
use vinyl\di\definition\value\StringValue;
use vinyl\di\definitionMapBuilder\definitionBuilder\Arguments;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\MutableMap;
use function assert;

/**
 * Class MapConfigurator
 */
final class MapConfigurator
{
    private Arguments $parent;
    private ?ArrayMapValue $mapValue = null;

    /** @var \vinyl\std\lang\collections\MutableMap<string, \vinyl\di\Definition> */
    private MutableMap $definitionMap;

    /**
     * MapConfigurator constructor.
     *
     * @param MutableMap<string, \vinyl\di\Definition> $definitionMap
     */
    public function __construct(Arguments $parent, MutableMap $definitionMap)
    {
        $this->parent = $parent;
        $this->definitionMap = $definitionMap;
    }

    /**
     * @param string|int $key
     */
    public function classItem($key, string $className): self
    {
        assert($this->mapValue !== null);

        if (!$this->definitionMap->containsKey($className)) {
            $classDefinition = new ClassDefinition(ClassObject::create($className));
            $this->definitionMap->put($classDefinition->id(), $classDefinition);
        }

        $this->mapValue->put($key, new StringValue($className));

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function objectItem($key, string $definitionId): self
    {
        assert($this->mapValue !== null);
        $itemValue = new ObjectValue($definitionId);
        $this->mapValue->put($key, $itemValue);

        return $this;
    }

    /**
     * @param int|string $key
     */
    public function proxyItem($key, ?string $value): self
    {
        assert($this->mapValue !== null);

        $itemValue = new ProxyValue($value);
        $this->mapValue->put($key, $itemValue);

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function intItem($key, ?int $value): self
    {
        assert($this->mapValue !== null);
        $this->mapValue->put($key, new IntValue($value));

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function floatItem($key, ?float $value): self
    {
        assert($this->mapValue !== null);
        $this->mapValue->put($key, new FloatValue($value));

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function boolItem($key, ?bool $value): self
    {
        assert($this->mapValue !== null);
        $this->mapValue->put($key, new BoolValue($value));

        return $this;
    }

    /**
     * @param string|int $key
     */
    public function stringItem($key, ?string $value): self
    {
        assert($this->mapValue !== null);
        $this->mapValue->put($key, new StringValue($value));

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
