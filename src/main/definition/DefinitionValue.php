<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Interface DefinitionValue
 */
interface DefinitionValue
{
    /**
     * Returns value stored in current object
     *
     * @return mixed
     */
    public function value();
}
