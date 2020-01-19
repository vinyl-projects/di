<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface ProxyValue
 */
interface ProxyValue extends DefinitionValue//, Mergeable
{
    /**
     * Returns definition id or null, if returned value is null then definition id should be taken from constructor parameter
     */
    public function value(): ?string;
}
