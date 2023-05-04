<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use UnitEnum;

interface EnumValue extends DefinitionValue
{

    /**
     * {@inheritDoc}
     */
    public function value(): ?UnitEnum;
}