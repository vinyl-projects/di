<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface StringValue
 */
interface StringValue extends DefinitionValue
{
    /**
     * {@inheritDoc}
     */
    public function value(): ?string;
}
