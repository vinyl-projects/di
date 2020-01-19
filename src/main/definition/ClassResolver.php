<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\Definition;

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
     * @return string Returns class name which associated with provided definition
     * @throws \vinyl\di\definition\DefinitionCircularReferenceFoundException If circular dependency has been found during class resolving process
     * @throws \vinyl\di\definition\ClassResolverException
     */
    public function resolve(Definition $definition, UnmodifiableDefinitionMap $definitionMap): string;
}
