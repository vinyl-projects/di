<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\NullValueException;
use vinyl\di\definition\value\EnumValue;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\EnumFactoryValue;
use vinyl\std\lang\collections\Map;
use function vinyl\di\isDeclaredTypeCompatibleWith;

final class EnumValueProcessor implements ValueProcessor
{

    /**
     * {@inheritDoc}
     */
    public function process(DefinitionValue $value, ConstructorValue $constructorValue, Map $definitionMap): ValueProcessorResult
    {
        assert($value instanceof EnumValue);

        $enumValue = $value->value();
        $type = $constructorValue->type();

        if ($enumValue === null && !$constructorValue->isNullable()) {
            throw NullValueException::create();
        }

        if ($enumValue !== null && !isDeclaredTypeCompatibleWith($type, $enumValue::class)) {
            throw IncompatibleTypeException::create($type, get_debug_type($enumValue));
        }

        $enumClass = $enumValue !== null ? get_class($enumValue) : null;
        $caseName = $enumValue?->name;

        return new ValueProcessorResult(new EnumFactoryValue($enumClass, $caseName, false));
    }
}
