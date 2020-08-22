<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;

/**
 * Class StaticMethodInstantiator
 */
final class StaticMethodInstantiator implements Instantiator
{
    private string $callableMethod;

    /** @var \ReflectionParameter[] */
    private array $parameters;

    /**
     * StaticMethodObjectInstantiator constructor.
     *
     * @psalm-param class-string $class
     */
    public function __construct(string $class, string $staticMethod)
    {
        try {
            $reflectionMethod = new ReflectionMethod($class, $staticMethod);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        if (!$reflectionMethod->isStatic()) {
            throw new InvalidArgumentException("{$class}::{$staticMethod} not static.");
        }

        if (!$reflectionMethod->isPublic()) {
            throw new InvalidArgumentException("{$class}::{$staticMethod} not public.");
        }

        /** @var \ReflectionNamedType|null $reflectionType */
        $reflectionType = $reflectionMethod->getReturnType();
        if ($reflectionType === null || $reflectionType->getName() === 'void') {
            throw new InvalidArgumentException("{$class}::{$staticMethod} have no return type.");
        }

        $this->parameters = $reflectionMethod->getParameters();
        $this->callableMethod = "{$class}::{$staticMethod}";
    }

    /**
     * Static constructor of {@see StaticMethodInstantiator}
     *
     * @psalm-param class-string $class
     */
    public static function create(string $class, string $staticMethod): self
    {
        return new self($class, $staticMethod);
    }

    /**
     * {@inheritDoc}
     */
    public function value(): string
    {
        return $this->callableMethod;
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return $this->parameters;
    }
}
