<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use Error;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use function assert;
use function class_exists;

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
    public function __construct(string $class)
    {
        assert(class_exists($class));

        try {
            $classReflection = new ReflectionClass($class);

            if (!$classReflection->hasMethod('__construct')) {
                $this->parameterList = [];

                return;
            }

            $constructor = $classReflection->getMethod('__construct');
        } catch (ReflectionException $e) {
            throw new Error("Impossible reflection exception. Details: {$e->getMessage()}", $e->getCode(), $e);
        }

        if (!$constructor->isPublic()) {
            throw new InvalidArgumentException("Method [{$class}::__construct] must not be private or protected.");
        }

        $this->parameterList = $constructor->getParameters();
    }

    /**
     * This implementation always throws {@see \RuntimeException}
     */
    public function value(): string
    {
        throw new RuntimeException('Method must not be called.');
    }

    /**
     * {@inheritDoc}
     */
    public function parameters(): array
    {
        return $this->parameterList;
    }
}
