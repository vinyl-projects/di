<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;
use vinyl\std\lang\ClassObject;
use function spl_object_id;

/**
 * Class CacheableClassResolver
 */
final class CacheableClassResolver implements ClassResolver
{
    /** @var array<string, ClassObject> */
    private array $cache = [];
    private ClassResolver $classResolver;
    public ?int $currentDefinitionMapId = null;

    /**
     * CacheablesResolver constructor.
     */
    public function __construct(?ClassResolver $classResolver = null)
    {
        $this->classResolver = $classResolver ?? new RecursionFreeClassResolver();
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Definition $definition, UnmodifiableDefinitionMap $definitionMap): ClassObject
    {
        $mapId = spl_object_id($definitionMap);

        if ($this->currentDefinitionMapId !== $mapId) {
            $this->currentDefinitionMapId = $mapId;
            $this->cleanCache();
        }

        return $this->cache[$definition->id()] ??= $this->classResolver->resolve($definition, $definitionMap);
    }

    /**
     * Cleans resolved class cache
     */
    public function cleanCache(): void
    {
        $this->cache = [];
    }
}
