<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface FloatValue
 */
interface FloatValue extends DefinitionValue
{
    /**
     * {@inheritDoc}
     */
    public function value(): ?float;
}
