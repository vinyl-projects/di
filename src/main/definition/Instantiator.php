<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface Instantiator
 */
interface Instantiator
{
    /**
     * Returns callable string or null if default constructor must be used
     */
    public function value(): ?string;

    /**
     * Returns instantiator parameters
     *
     * @return \ReflectionParameter[]
     */
    public function parameters(): array;
}
