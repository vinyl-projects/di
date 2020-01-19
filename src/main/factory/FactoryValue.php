<?php

declare(strict_types=1);

namespace vinyl\di\factory;

/**
 * Interface FactoryValue
 */
interface FactoryValue
{
    /**
     * Checks whether value for particular argument is missed
     *
     * If returned value is **True** it means that value is not configured for argument and default value is not available.
     */
    public function isMissed(): bool;

    /**
     * Returns value stored in this object
     *
     * @return mixed
     */
    public function value();
}
