<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use InvalidArgumentException;
use RuntimeException;
use vinyl\di\definition\BoolValue;
use vinyl\di\definition\ClassResolver;
use vinyl\di\definition\ClassResolverAware;
use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\FloatValue;
use vinyl\di\definition\IntValue;
use vinyl\di\definition\ListValue;
use vinyl\di\definition\MapValue;
use vinyl\di\definition\ObjectValue;
use vinyl\di\definition\StringValue;
use vinyl\di\definition\value\EnumValue;
use vinyl\di\definition\value\NoValue;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\std\lang\collections\Map;
use function array_key_exists;
use function array_replace;
use function get_class;
use function is_a;

/**
 * Class ValueProcessorCompositor
 */
final class ValueProcessorCompositor implements ValueProcessor
{
    /** @var array<string, ValueProcessor> */
    private array $valueTypeProcessorMap = [];

    /** @var array<string, string> */
    private array $processorMap = [];
    private ClassResolver $classResolver;

    /**
     * ValueProcessorComposite constructor.
     *
     * @param array<string, ValueProcessor> $processorMap indexed by value type
     */
    public function __construct(?array $processorMap, ClassResolver $classResolver)
    {
        $processorMap = $processorMap ?? [];
        $this->classResolver = $classResolver;
        $arrayValueProcessor = new ArrayValueProcessor($this);
        $defaultProcessorMap = [
            NoValue::class     => new NoValueProcessor(),
            ObjectValue::class => new ObjectValueProcessor(),
            StringValue::class => new StringValueProcessor(),
            MapValue::class    => $arrayValueProcessor,
            ListValue::class   => $arrayValueProcessor,
            IntValue::class    => new IntValueProcessor(),
            FloatValue::class  => new FloatValueProcessor(),
            BoolValue::class   => new BoolValueProcessor(),
            EnumValue::class   => new EnumValueProcessor()
        ];

        $arrayReplace = array_replace($defaultProcessorMap, $processorMap);

        foreach ($arrayReplace as $interface => $valueProcessor) {
            $this->addValueProcessor($interface, $valueProcessor);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        Map $definitionMap
    ): ValueProcessorResult {
        $valueClass = get_class($value);

        if (array_key_exists($valueClass, $this->processorMap)) {
            $valueType = $this->processorMap[$valueClass];

            return $this->valueTypeProcessorMap[$valueType]->process($value, $constructorValue, $definitionMap);
        }

        foreach ($this->valueTypeProcessorMap as $valueType => $_) {
            if (!$value instanceof $valueType) {
                continue;
            }

            $this->processorMap[$valueClass] = $valueType;

            return $this->valueTypeProcessorMap[$valueType]->process($value, $constructorValue, $definitionMap);
        }

        throw new RuntimeException("Processor for [$valueClass] is not registered.");
    }

    private function addValueProcessor(string $valueType, ValueProcessor $valueProcessor): void
    {
        if (!is_a($valueType, DefinitionValue::class, true)) {
            throw new InvalidArgumentException("Given value type not exists. [{$valueType}]");
        }

        if ($valueProcessor instanceof ClassResolverAware) {
            $valueProcessor->injectClassResolver($this->classResolver);
        }

        $this->valueTypeProcessorMap[$valueType] = $valueProcessor;
    }
}
