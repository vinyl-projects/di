<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\IntValue;
use vinyl\di\definition\NullValueException;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\BuiltinFactoryValue;
use vinyl\std\lang\collections\Map;
use function assert;

/**
 * Class IntValueProcessor
 */
final class IntValueProcessor implements ValueProcessor
{
    /**
     * {@inheritDoc}
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        Map $definitionMap
    ): ValueProcessorResult {
        assert($value instanceof IntValue);

        $intValue = $value->value();
        $type = $constructorValue->type();

        if ($intValue === null && !$constructorValue->isNullable()) {
            throw NullValueException::create();
        }

        if ($type !== 'int' && $type !== 'float' && $type !== 'mixed') {
            throw IncompatibleTypeException::create($type, 'int');
        }

        return new ValueProcessorResult(new BuiltinFactoryValue($intValue, false));
    }
}
