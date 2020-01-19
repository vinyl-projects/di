<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;
use function spl_object_id;

/**
 * Class CacheableValueCollector
 */
final class CacheableValueCollector implements ValueCollector
{
    /** @var array<int, array<string, ValueMap>> */
    private array $cache = [];
    private ValueCollector $delegate;

    /**
     * CacheableValueCollector constructor.
     */
    public function __construct(?ValueCollector $delegate = null)
    {
        $this->delegate = $delegate ?? new RecursionFreeValueCollector();
    }

    /**
     * @inheritDoc
     */
    public function collect(Definition $definition, UnmodifiableDefinitionMap $definitionMap): ValueMap
    {
        $mapId = spl_object_id($definitionMap);

        return $this->cache[$mapId][$definition->id()] ??= $this->delegate->collect($definition, $definitionMap);
    }
}
