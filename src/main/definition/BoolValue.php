<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface BoolValue
 */
interface BoolValue extends DefinitionValue
{
    /**
     * {@inheritDoc}
     */
    public function value(): ?bool;
}
