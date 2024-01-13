<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\di\definition\DefinitionTransformer;
use vinyl\di\factory\FactoryMetadata;
use vinyl\std\lang\collections\Map;
use vinyl\std\lang\collections\MutableMap;
use function vinyl\std\lang\collections\mutableMapOf;

class LazyFactoryMetadataProvider
{
    /** @var \vinyl\std\lang\collections\MutableMap<string, \vinyl\di\factory\FactoryMetadata> */
    private MutableMap $factoryMetadataMap;

    /**
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     */
    public function __construct(
        private readonly Map $definitionMap,
        private readonly DefinitionTransformer $definitionTransformer
    ) {
        $this->factoryMetadataMap = mutableMapOf();
    }

    public function get(string $definitionId): FactoryMetadata
    {
        $this->transform($definitionId);

        if (!$this->factoryMetadataMap->containsKey($definitionId)) {
            throw new NotFoundException("[{$definitionId}] not found.");
        }

        return $this->factoryMetadataMap->get($definitionId);
    }

    public function has(string $definitionId): bool
    {
        $this->transform($definitionId);

        return $this->factoryMetadataMap->containsKey($definitionId);
    }

    private function transform(string $definitionId): void
    {
        if (!$this->factoryMetadataMap->containsKey($definitionId) && $this->definitionMap->containsKey($definitionId)) {
            $factoryMetadataMap = $this->definitionTransformer->transform(
                $this->definitionMap->get($definitionId),
                $this->definitionMap
            );

            $this->factoryMetadataMap->putAll($factoryMetadataMap);
        }
    }
}
