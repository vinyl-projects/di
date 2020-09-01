<?php

declare(strict_types=1);

namespace vinyl\di\factory;

use vinyl\di\definition\DefinitionTransformer;
use vinyl\di\definition\RecursiveDefinitionTransformer;
use vinyl\std\lang\collections\Map;
use function vinyl\std\lang\collections\mutableMapOf;

/**
 * Class DefinitionMapTransformer
 */
final class DefinitionMapTransformer
{
    private DefinitionTransformer $definitionTransformer;

    /**
     * MetadataBuilder constructor.
     */
    public function __construct(?DefinitionTransformer $definitionTransformer = null)
    {
        $this->definitionTransformer = $definitionTransformer ?? new RecursiveDefinitionTransformer();
    }

    /**
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     *
     * @throws \vinyl\di\definition\ClassCircularReferenceFoundException
     * @throws \vinyl\di\definition\DefinitionTransformerException
     *
     * @return Map<string, \vinyl\di\factory\FactoryMetadata>
     */
    public function transform(Map $definitionMap): Map
    {
        /** @var \vinyl\std\lang\collections\MutableMap<string, \vinyl\di\factory\FactoryMetadata> $factoryMetadataMap */
        $factoryMetadataMap = mutableMapOf();
        /** @var \vinyl\di\Definition $definition */
        foreach ($definitionMap as $definition) {
            if ($factoryMetadataMap->containsKey($definition->id())) {
                continue;
            }

            $factoryMetadataMap->putAll($this->definitionTransformer->transform($definition, $definitionMap));
        }

        return $factoryMetadataMap;
    }
}
