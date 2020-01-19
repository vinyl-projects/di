<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\definition\constructorMetadata\BuiltinConstructorValue;
use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionToDependencyMap;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\ListValue;
use vinyl\di\definition\MapValue;
use vinyl\di\definition\NullValueException;
use vinyl\di\definition\UnmodifiableDefinitionMap;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorException;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\factory\argument\ArrayValue;
use function assert;

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
     * @param MapValue $value
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        UnmodifiableDefinitionMap $definitionMap
    ): ValueProcessorResult {
        assert($value instanceof MapValue || $value instanceof ListValue);

        $value->sort();
        /** @var \vinyl\di\definition\DefinitionValue[]|null $values */
        $values = $value->value();

        if ($values === null && !$constructorValue->isNullable()) {
            throw NullValueException::create();
        }

        $type = $constructorValue->type();
        if ($type !== 'array' && $type !== 'iterable' && $type !== 'mixed') {
            throw IncompatibleTypeException::create($type, 'array');
        }

        if ($values === null) {
            return new ValueProcessorResult(ArrayValue::createNullValue());
        }

        $argumentValue = [];
        $definitionToDependencyMap = new DefinitionToDependencyMap();

        $mixedValue = new BuiltinConstructorValue(null, 'mixed', true, false, false);
        foreach ($values as $key => $item) {
            try {
                $result = $this->valueProcessorComposite->process($item, $mixedValue, $definitionMap);
            } catch (ValueProcessorException $e) {
                throw new ValueProcessorException(
                    "An error occurred during processing an array item with key [{$key}]. Details: {$e->getMessage()}",
                    $e->getCode(),
                    $e
                );
            }

            if ($result->definitionToDependencyMap !== null) {
                $definitionToDependencyMap->add($result->definitionToDependencyMap);
            }

            $argumentValue[$key] = $result->valueMetadata;
        }

        return new ValueProcessorResult(
            new ArrayValue($argumentValue, false),
            $definitionToDependencyMap
        );
    }
}
