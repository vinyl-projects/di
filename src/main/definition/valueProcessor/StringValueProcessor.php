<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\NullValueException;
use vinyl\di\definition\StringValue;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\BuiltinFactoryValue;
use vinyl\std\lang\collections\Map;
use function assert;
use function vinyl\di\isDeclaredTypeCompatibleWith;

/**
 * Class StringValueProcessor
 */
final class StringValueProcessor implements ValueProcessor
{
    /**
     * {@inheritDoc}
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        Map $definitionMap
    ): ValueProcessorResult {
        assert($value instanceof StringValue);

        $stringValue = $value->value();

        if ($stringValue === null && !$constructorValue->isNullable()) {
            throw NullValueException::create();
        }

        if (!isDeclaredTypeCompatibleWith($constructorValue->type(), 'string')) {
            throw IncompatibleTypeException::create($constructorValue->type(), 'string');
        }

        return new ValueProcessorResult(new BuiltinFactoryValue($value->value(), false));
    }
}
