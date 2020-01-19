<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\value;

use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\value\ObjectValue;

/**
 * Class ObjectValueTest
 */
class ObjectValueTest extends AbstractObjectValueTest
{
    /**
     * {@inheritDoc}
     */
    protected function createValue(string $definitionId, ...$additionalArguments): DefinitionValue
    {
        return new ObjectValue($definitionId);
    }
}
