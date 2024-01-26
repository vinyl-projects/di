<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\std\lang\Entry;

/**
 * Class RuntimeDefinition
 *
 * Definition to mark classes/interfaces that must be injected to DI after instantiation
 */
final class RuntimeDefinition extends AbstractDefinition
{
    /**
     * RuntimeDefinition constructor.
     */
    public function __construct(Entry $class)
    {
        parent::__construct($class->name());
    }
}
