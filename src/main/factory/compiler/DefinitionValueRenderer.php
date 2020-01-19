<?php

declare(strict_types=1);

namespace vinyl\di\factory\compiler;

use vinyl\di\factory\argument\DefinitionFactoryValue;
use vinyl\di\factory\FactoryValue;
use function assert;

/**
 * Class DefinitionValueRenderer
 */
final class DefinitionValueRenderer implements ValueRenderer
{
    /**
     * {@inheritDoc}
     */
    public function render(FactoryValue $value): string
    {
        assert($value instanceof DefinitionFactoryValue);

        $definitionId = $value->value();
        if ($definitionId === null) {

            return 'null';
        }

        return "\$di->get('{$definitionId}')";
    }
}
