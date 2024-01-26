<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\di\definition\Instantiator;
use vinyl\di\definition\Lifetime;
use vinyl\std\lang\collections\MutableMap;
use function vinyl\std\lang\collections\mutableMapOf;

/**
 * Class AbstractDefinition
 */
abstract class AbstractDefinition implements Definition
{
    private string $id;
    /** todo remove this property from abstract */

    private ?Lifetime $lifetime = null;
    private bool $argumentInheritance = false;

    /** @var MutableMap<string, \vinyl\di\definition\DefinitionValue> */
    private MutableMap $argumentValues;
    private ?Instantiator $instantiator = null;

    /**
     * AbstractDefinition constructor.
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->argumentValues = mutableMapOf();
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
    public function instantiator(): ?Instantiator
    {
        return $this->instantiator;
    }

    /**
     * {@inheritDoc}
     */
    public function changeInstantiator(?Instantiator $objectInstantiator): void
    {
        $this->instantiator = $objectInstantiator;
    }

    /**
     * {@inheritDoc}
     */
    public function changeLifetime(?Lifetime $lifetime): void
    {
        $this->lifetime = $lifetime;
    }

    /**
     * {@inheritDoc}
     */
    public function lifetime(): ?Lifetime
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
    public function argumentValues(): MutableMap
    {
        return $this->argumentValues;
    }
}
