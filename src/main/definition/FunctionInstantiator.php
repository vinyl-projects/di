<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use InvalidArgumentException;
use ReflectionException;
use ReflectionFunction;

/**
 * Class FunctionObjectInstantiator
 */
final class FunctionInstantiator implements Instantiator
{
    private string $function;

    /** @var \ReflectionParameter[] */
    private array $parameters;

    /**
     * FunctionObjectInstantiator constructor.
     *
     * @param string $function function name
     */
    public function __construct(string $function)
    {
        try {
            $functionReflection = new ReflectionFunction($function);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        /** @var \ReflectionNamedType|null $reflectionType */
        $reflectionType = $functionReflection->getReturnType();
        if ($reflectionType === null || $reflectionType->getName() === 'void') {
            throw new InvalidArgumentException("{$functionReflection->getName()} have no return type.");
        }

        $this->parameters = $functionReflection->getParameters();
        $this->function = $function;
    }

    public static function create(string $function): self
    {
        return new self($function);
    }

    /**
     * {@inheritDoc}
     */
    public function value(): string
    {
        return $this->function;
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return $this->parameters;
    }
}
