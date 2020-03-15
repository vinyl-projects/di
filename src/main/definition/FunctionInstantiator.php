<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use InvalidArgumentException;
use ReflectionException;
use ReflectionFunction;
use function assert;
use function function_exists;

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
        assert(function_exists($function));

        try {
            $functionReflection = new ReflectionFunction($function);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
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
