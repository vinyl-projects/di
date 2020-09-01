<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\FloatValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\NullValueException;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\BuiltinFactoryValue;
use vinyl\std\lang\collections\Map;
use function assert;

/**
 * Class FloatValueProcessor
 */
final class FloatValueProcessor implements ValueProcessor
{
    /**
     * {@inheritDoc}
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        Map $definitionMap
    ): ValueProcessorResult {
        assert($value instanceof FloatValue);

        $floatValue = $value->value();
        $type = $constructorValue->type();

        if ($floatValue === null && !$constructorValue->isNullable()) {
            throw NullValueException::create();
        }

        if ($type !== 'float' && $type !== 'mixed') {
            throw IncompatibleTypeException::create($type, 'float');
        }

        return new ValueProcessorResult(new BuiltinFactoryValue($floatValue, false));
    }
}
