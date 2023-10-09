<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\ListValue;
use vinyl\di\definition\MapValue;
use vinyl\di\definition\NullValueException;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorException;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\ArrayValue;
use vinyl\std\lang\collections\Map;
use function assert;
use function vinyl\di\isDeclaredTypeCompatibleWith;
use function vinyl\std\lang\collections\mutableVectorOf;

/**
 * Class ArrayValueProcessor
 */
final class ArrayValueProcessor implements ValueProcessor
{
    private ValueProcessorCompositor $valueProcessorComposite;

    /**
     * MapValueProcessor constructor.
     */
    public function __construct(ValueProcessorCompositor $valueProcessorComposite)
    {
        $this->valueProcessorComposite = $valueProcessorComposite;
    }

    /**
     * {@inheritDoc}
     */
    public function process(
        DefinitionValue  $value,
        ConstructorValue $constructorValue,
        Map              $definitionMap
    ): ValueProcessorResult
    {
        assert($value instanceof MapValue || $value instanceof ListValue);

        $values = $value->value();

        if ($values === null && !$constructorValue->isNullable()) {
            throw NullValueException::create();
        }

        if (!isDeclaredTypeCompatibleWith($constructorValue->type(), 'array')
            && !isDeclaredTypeCompatibleWith($constructorValue->type(), 'iterable')) {
            throw IncompatibleTypeException::create($constructorValue->type(), 'array');
        }

        if ($values === null) {
            return new ValueProcessorResult(ArrayValue::createNullValue());
        }

        $argumentValue = [];

        /** @var \vinyl\std\lang\collections\MutableVector<\vinyl\di\definition\DefinitionDependency> $dependencies */
        $dependencies = mutableVectorOf();

        $mixedValue = new ConstructorValue(null, null, true, false, false);
        foreach ($values as $key => $item) {
            try {
                $result = $this->valueProcessorComposite->process($item, $mixedValue, $definitionMap);
            } catch (ValueProcessorException $e) {
                throw new ValueProcessorException(
                    "An error occurred during processing an array item with key [{$key}]. Details: {$e->getMessage()}",
                    0,
                    $e
                );
            }

            if ($result->dependencies !== null) {
                $dependencies->addAll($result->dependencies);
            }

            $argumentValue[$key] = $result->valueMetadata;
        }

        return new ValueProcessorResult(
            new ArrayValue($argumentValue, false),
            $dependencies
        );
    }
}
