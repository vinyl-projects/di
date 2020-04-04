<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use Error;
use InvalidArgumentException;
use ReflectionException;
use vinyl\std\ClassObject;

/**
 * Class ConstructorInstantiator
 *
 * @internal
 */
final class ConstructorInstantiator implements Instantiator
{
    /** @var \ReflectionParameter[] */
    private array $parameterList;

    /**
     * ConstructorInstantiator constructor.
     *
     * @throws \InvalidArgumentException if constructor for given class have private or protected visibility
     */
    public function __construct(ClassObject $class)
    {
        try {
            $classReflection = $class->toReflectionClass();

            if (!$classReflection->hasMethod('__construct')) {
                $this->parameterList = [];

                return;
            }

            $constructor = $classReflection->getMethod('__construct');
        } catch (ReflectionException $e) {
            throw new Error("Impossible reflection exception. Details: {$e->getMessage()}", $e->getCode(), $e);
        }

        if (!$constructor->isPublic()) {
            throw new InvalidArgumentException("Constructor method [{$class->className()}::__construct] must not be private or protected.");
        }

        $this->parameterList = $constructor->getParameters();
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return $this->parameterList;
    }
}
