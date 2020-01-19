<?php

declare(strict_types=1);

namespace vinyl\di\factory\argument;

use vinyl\di\factory\FactoryValue;

/**
 * Class DefinitionValue
 */
final class DefinitionFactoryValue implements FactoryValue
{
    private ?string $definitionId;
    private bool $isMissed;

    /**
     * ObjectValue constructor.
     */
    public function __construct(?string $definitionId, bool $isMissed)
    {
        $this->definitionId = $definitionId;
        $this->isMissed = $isMissed;
    }

    /**
     * {@inheritDoc}
     *
     * @return string|null An Id of definition that must be instantiated and passed as argument or null.
     * If null is returned then null must be passed instead of object
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
