<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface ObjectValue
 */
interface ObjectValue extends DefinitionValue
{
    /**
     * Returns definition id
     */
    public function value(): ?string;
}
