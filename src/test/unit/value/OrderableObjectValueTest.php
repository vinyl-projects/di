<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\value;

use vinyl\di\definition\arrayValue\OrderableObjectValue;
use vinyl\di\definition\DefinitionValue;

/**
 * Class OrderableObjectValueTest
 */
class OrderableObjectValueTest extends AbstractObjectValueTest
{
    /**
     * {@inheritDoc}
     */
    protected function createValue(string $definitionId, ...$additionalArguments): DefinitionValue
    {
        return new OrderableObjectValue($definitionId, ...$additionalArguments);
    }
}
