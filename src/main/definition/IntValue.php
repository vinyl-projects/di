<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface IntValue
 */
interface IntValue extends DefinitionValue
{
    /**
     * {@inheritDoc}
     */
    public function value(): ?int;
}
