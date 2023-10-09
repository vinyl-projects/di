<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\definition\BoolValue;
use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\NullValueException;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\BuiltinFactoryValue;
use vinyl\std\lang\collections\Map;
use function assert;
use function vinyl\di\isDeclaredTypeCompatibleWith;

/**
 * Class BoolValueProcessor
 */
final class BoolValueProcessor implements ValueProcessor
{
    /**
     * {@inheritDoc}
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        Map $definitionMap
    ): ValueProcessorResult
    {
        assert($value instanceof BoolValue);

        $boolValue = $value->value();
        $type = $constructorValue->type();

        if ($boolValue === null && !$constructorValue->isNullable()) {
            throw NullValueException::create();
        }

        if ($type === null) {
            return new ValueProcessorResult(new BuiltinFactoryValue($boolValue, false));
        }

        if (!isDeclaredTypeCompatibleWith($type, 'bool')) {
            throw IncompatibleTypeException::create($type, 'bool');
        }

        return new ValueProcessorResult(new BuiltinFactoryValue($boolValue, false));
    }
}
