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

    /** @var string|null Contains callable string which should be used as constructor, if value is null, default class constructor must be used */
    public ?string $constructor;

    /** This value will be <code>False</code> in case this factory contain {@see FactoryValue} that are missed */
    public bool $isComplete = true;

    /**
     * FactoryMetadata constructor.
     */
    public function __construct(string $id, string $class, ?string $constructor)
    {
        $this->id = $id;
        $this->class = $class;
        $this->constructor = $constructor;
    }
}
