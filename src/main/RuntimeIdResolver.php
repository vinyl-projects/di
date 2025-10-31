<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\std\lang\collections\Map;

/**
 * Class RuntimeAliasIdResolver
 *
 * The RuntimeAliasIdResolver class is an implementation of the AliasIdResolver interface that resolves aliases at runtime
 * primarily used is for Dev mode
 */
final readonly class RuntimeIdResolver implements IdResolver
{
    /**
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     */
    public function __construct(private Map $definitionMap)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(string $id): string
    {
        $definition = $this->definitionMap->find($id);
        if ($definition instanceof AliasDefinition) {
            return $definition->definitionId();
        }

        return $id;
    }
}
