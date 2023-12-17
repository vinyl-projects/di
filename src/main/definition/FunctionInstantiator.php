<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use Closure;
use InvalidArgumentException;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;

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
     * @psalm-param callable-string $function
     */
    public function __construct(ReflectionFunction $functionReflection)
    {
        $reflectionType = $functionReflection->getReturnType();

        $nameParts = [];
        if ($functionReflection->getClosureScopeClass() !== null) {
            $nameParts[] = $functionReflection->getClosureScopeClass()->getName();
            $nameParts[] = '::';
        }

        $nameParts[] = $functionReflection->getName();
        $name = implode('', $nameParts);

        if (!$reflectionType instanceof ReflectionNamedType && $reflectionType !== null) {
            throw new InvalidArgumentException("[$name] must not have union or intersection type.");
        }

        if ($reflectionType === null || $reflectionType->getName() === 'void') {
            throw new InvalidArgumentException("{$name} have no return type.");
        }

        $this->parameters = $functionReflection->getParameters();
        $this->function = $name;
    }

    /**
     * @psalm-param callable-string $function
     */
    public static function create(string $function): self
    {
        try {
            $functionReflection = new ReflectionFunction($function);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        return new self($functionReflection);
    }

    public static function createFromClosure(Closure $closure): self
    {
        try {
            $functionReflection = new ReflectionFunction($closure);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        return new self($functionReflection);
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
