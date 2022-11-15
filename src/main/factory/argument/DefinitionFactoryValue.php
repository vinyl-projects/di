<?php

declare(strict_types=1);

namespace vinyl\di\factory\argument;

use vinyl\di\factory\FactoryValue;

/**
 * Class DefinitionValue
 */
final class DefinitionFactoryValue implements FactoryValue
{
    /**
     * ObjectValue constructor.
     */
    public function __construct(private readonly ?string $definitionId, private readonly bool $isMissed)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @return string|null An Id of definition that must be instantiated and passed as argument or null.
     * If null is returned then it must be passed instead of object
     */
    public function value(): ?string
    {
        return $this->definitionId;
    }

    /**
     * {@inheritDoc}
     */
    public function isMissed(): bool
    {
        return $this->isMissed;
    }
}
