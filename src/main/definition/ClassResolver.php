<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;

/**
 * Interface ClassResolver
 */
interface ClassResolver
{
    /**
     * Resolve class name for definition
     *
     * The resolve process involves looking up other definitions
     *
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     *
     * @return \vinyl\std\lang\ClassObject Returns class object which associated with provided definition
     * @throws \vinyl\di\definition\DefinitionCircularReferenceFoundException If circular dependency has been found during class resolving process
     * @throws \vinyl\di\definition\ClassResolverException
     */
    public function resolve(Definition $definition, Map $definitionMap): ClassObject;
}
