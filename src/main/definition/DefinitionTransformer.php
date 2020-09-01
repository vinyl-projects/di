<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;
use vinyl\std\lang\collections\Map;

/**
 * Interface DefinitionTransformer
 */
interface DefinitionTransformer
{
    /**
     * @psalm-param Map<string, \vinyl\di\Definition> $definitionMap
     *
     * @throws DefinitionTransformerException is thrown in case given {@see Definition} cannot be transformed to {@see \vinyl\di\factory\FactoryMetadata} Map
     * @throws \vinyl\di\definition\ClassCircularReferenceFoundException is thrown in case circular reference between {@see Definition} is found
     *
     * @return Map<string, \vinyl\di\factory\FactoryMetadata>
     */
    public function transform(Definition $definition, Map $definitionMap): Map;
}
