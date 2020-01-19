<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;
use function spl_object_id;

/**
 * Class CacheableClassResolver
 */
final class CacheableClassResolver implements ClassResolver
{
    private ClassResolver $classResolver;

    /** @var array<int, array<string, string>> */
    private array $cache = [];

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
    public function resolve(Definition $definition, UnmodifiableDefinitionMap $definitionMap): string
    {
        $mapId = spl_object_id($definitionMap);

        return $this->cache[$mapId][$definition->id()] ??= $this->classResolver->resolve($definition, $definitionMap);
    }
}
