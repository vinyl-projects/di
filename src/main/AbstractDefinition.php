<?php

declare(strict_types=1);

namespace vinyl\di;

use InvalidArgumentException;
use LogicException;
use vinyl\di\definition\Lifetime;
use vinyl\di\definition\SingletonLifetime;
use vinyl\di\definition\ValueMap;
use function class_exists;

/**
 * Class AbstractDefinition
 */
abstract class AbstractDefinition implements Definition
{
    private string $id;
    /** todo remove this property from abstract */
    protected ?string $className;
    private Lifetime $lifetime;
    private bool $argumentInheritance = false;
    private ValueMap $argumentValues;
    private ?string $constructorMethodName = null;

    /**
     * AbstractDefinition constructor.
     */
    public function __construct(
        string $id,
        ?string $class
    ) {
        $this->id = $id;

        if ($class !== null && !class_exists($class)) {
            throw new InvalidArgumentException("Class {$class} for definition with id {$id} not exists.");
        }

        if ($class !== null && $class[0] === '\\') {
            throw new InvalidArgumentException('The class name of definition can\'t be started from backslash.');
        }

        $this->className = $class;
        $this->lifetime = SingletonLifetime::get();
        $this->argumentValues = new ValueMap();
    }

    /**
     * {@inheritDoc}
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function constructorMethodName(): ?string
    {
        return $this->constructorMethodName;
    }

    /**
     * {@inheritDoc}
     */
    public function changeConstructorMethod(string $methodName): void
    {
        if ($methodName === '') {
            throw new InvalidArgumentException('Method name could not be empty.');
        }

        $this->constructorMethodName = $methodName;
    }

    /**
     * {@inheritDoc}
     */
    public function changeLifetime(Lifetime $lifetime): void
    {
        $this->lifetime = $lifetime;
    }

    /**
     * {@inheritDoc}
     */
    public function lifetime(): Lifetime
    {
        return $this->lifetime;
    }

    /**
     * {@inheritDoc}
     */
    public function isArgumentInheritanceEnabled(): bool
    {
        return $this->argumentInheritance;
    }

    /**
     * {@inheritDoc}
     */
    public function toggleArgumentInheritance(bool $status): ?bool
    {
        if ($this->argumentInheritance === $status) {
            return null;
        }

        $this->argumentInheritance = $status;

        return !$status;
    }

    /**
     * {@inheritDoc}
     */
    public function className(): string
    {
        if ($this->className === null) {
            throw new LogicException('Class name must be initialized.');
        }

        return $this->className;
    }

    /**
     * {@inheritDoc}
     */
    public function replaceClass(string $newCLass): string
    {
        if ($this->className === null) {
            throw new LogicException('You have to initialize class first, before trying replacing it.');
        }

        if ($newCLass === '') {
            throw new InvalidArgumentException('Class name could not be empty.');
        }

        if (!class_exists($newCLass)) {
            throw new InvalidArgumentException("Given class [{$newCLass}] not exists.");
        }

        $oldClass = $this->className;
        $this->className = $newCLass;

        return $oldClass;
    }

    /**
     * {@inheritDoc}
     */
    public function argumentValues(): ValueMap
    {
        return $this->argumentValues;
    }
}
