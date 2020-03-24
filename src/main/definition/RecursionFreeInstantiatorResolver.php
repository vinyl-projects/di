<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use LogicException;
use vinyl\di\Definition;

/**
 * Class RecursionFreeInstantiatorResolver
 */
final class RecursionFreeInstantiatorResolver implements InstantiatorResolver
{
    private ClassResolver $classResolver;

    /**
     * RecursionFreeInstantiatorResolver constructor.
     */
    public function __construct(ClassResolver $classResolver)
    {
        $this->classResolver = $classResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Definition $definition, DefinitionMap $definitionMap): Instantiator
    {
        if ($definition->instantiator() !== null) {
            return $definition->instantiator();
        }

        try {
            $class = $this->classResolver->resolve($definition, $definitionMap);
        } catch (ClassResolverException $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }

        return new ConstructorInstantiator($class);
    }
}
