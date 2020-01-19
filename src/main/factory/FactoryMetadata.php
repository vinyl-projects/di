<?php

declare(strict_types=1);

namespace vinyl\di\factory;

/**
 * Class FactoryMetadata
 */
final class FactoryMetadata
{
    /** unique id of factory */
    public string $id;

    /** Class name */
    public string $class;

    /** @var array<string, \vinyl\di\factory\FactoryValue> indexed by argument name */
    public array $values = [];

    /** contains public static method name which should be used as constructor, if value is null, default constructor must be used */
    public ?string $constructorMethodName;

    /** This value will be <code>False</code> in case this factory contain {@see FactoryValue} that are missed */
    public bool $isComplete = true;

    /**
     * FactoryMetadata constructor.
     */
    public function __construct(string $id, string $class, ?string $constructorMethodName)
    {
        $this->id = $id;
        $this->class = $class;
        $this->constructorMethodName = $constructorMethodName;
    }
}
