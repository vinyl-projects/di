<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;
use vinyl\di\factory\FactoryMetadataMap;

/**
 * Interface DefinitionTransformer
 */
interface DefinitionTransformer
{
    /**
     * @throws DefinitionTransformerException is thrown in case given {@see Definition} cannot be transformed to {@see FactoryMetadataMap}
     * @throws \vinyl\di\definition\ClassCircularReferenceFoundException is thrown in case circular reference between {@see Definition} is found
     */
    public function transform(Definition $definition, DefinitionMap $definitionMap): FactoryMetadataMap;
}
