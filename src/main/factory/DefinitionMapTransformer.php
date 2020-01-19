<?php

declare(strict_types=1);

namespace vinyl\di\factory;

use vinyl\di\definition\DefinitionMap;
use vinyl\di\definition\DefinitionTransformer;
use vinyl\di\definition\RecursiveDefinitionTransformer;

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
     * @throws \vinyl\di\definition\ClassCircularReferenceFoundException
     * @throws \vinyl\di\definition\DefinitionTransformerException
     */
    public function transform(DefinitionMap $definitionMap): FactoryMetadataMap
    {
        $factoryMetadataMap = new FactoryMetadataMap();
        /** @var \vinyl\di\Definition $definition */
        foreach ($definitionMap as $definition) {
            if ($factoryMetadataMap->contains($definition->id())) {
                continue;
            }

            $factoryMetadataMap->add($this->definitionTransformer->transform($definition, $definitionMap));
        }

        return $factoryMetadataMap;
    }
}
