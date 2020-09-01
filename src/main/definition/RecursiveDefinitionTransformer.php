<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use InvalidArgumentException;
use vinyl\di\Definition;
use vinyl\di\definition\value\NoValue;
use vinyl\di\definition\valueProcessor\ValueProcessorCompositor;
use vinyl\di\factory\FactoryMetadata;
use vinyl\std\lang\collections\Map;
use vinyl\std\lang\collections\MutableMap;
use function array_key_exists;
use function vinyl\std\lang\collections\mutableMapOf;

/**
 * Class RecursiveDefinitionTransformer
 */
final class RecursiveDefinitionTransformer implements DefinitionTransformer
{
    private ConstructorValueExtractor $constructorValueExtractor;
    private ClassResolver $classResolver;
    private ValueProcessor $valueProcessor;
    private ValueCollector $valueCollector;
    private InstantiatorResolver $instantiatorResolver;
    private LifetimeResolver $lifetimeResolver;

    /**
     * ClassFactoryMetadataBuilder constructor.
     */
    public function __construct(
        ?ValueProcessor $valueProcessor = null,
        ?ClassResolver $classResolver = null,
        ?LifetimeResolver $lifetimeResolver = null,
        ?InstantiatorResolver $instantiatorResolver = null,
        ?ValueCollector $valueCollector = null,
        ?ConstructorValueExtractor $constructorValueExtractor = null
    ) {
        $this->classResolver = $classResolver ?? new RecursionFreeClassResolver();
        $this->valueProcessor = $valueProcessor ?? new ValueProcessorCompositor(null, $this->classResolver);
        $this->constructorValueExtractor = $constructorValueExtractor ?? new ConstructorValueExtractor();
        $this->valueCollector = $valueCollector ?? new RecursionFreeValueCollector();
        $this->instantiatorResolver = $instantiatorResolver ?? new RecursionFreeInstantiatorResolver($this->classResolver);
        $this->lifetimeResolver = $lifetimeResolver ?? new RecursionFreeLifetimeResolver();
    }

    /**
     * {@inheritDoc}
     */
    public function transform(Definition $definition, Map $definitionMap): Map
    {
        $factoryMetadataMap = mutableMapOf();
        $this->internalTransform($definition, $definitionMap, $factoryMetadataMap);

        return $factoryMetadataMap;
    }

    /**
     * @param array<string, string> $visitedClasses
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     *
     * @throws \vinyl\di\definition\DefinitionTransformerException
     */
    private function internalTransform(
        Definition $definition,
        Map $definitionMap,
        MutableMap $factoryMetadataMap,
        array $visitedClasses = []
    ): void {
        $id = $definition->id();

        try {
            $class = $this->classResolver->resolve($definition, $definitionMap);
        } catch (DefinitionCircularReferenceFoundException $e) {
            throw new DefinitionTransformerException("Circular reference found during class resolving for [{$id}] definition. Chain: {$e->getMessage()}");
        } catch (ClassResolverException $e) {
            throw DefinitionTransformerException::createFromException(
                "An error occurred, while class resolving for [{$id}] definition. Details: {$e->getMessage()}",
                $e
            );
        }

        $className = $class->name();
        if (array_key_exists($className, $visitedClasses)) {
            throw ClassCircularReferenceFoundException::create($className, $visitedClasses);
        }

        if ($factoryMetadataMap->containsKey($id)) {
            return;
        }

        $visitedClasses[$className] = $id;
        $objectInstantiator = $this->instantiatorResolver->resolve($definition, $definitionMap);
        $lifetime = $this->lifetimeResolver->resolve($definition, $definitionMap);
        $factoryMetadata = new FactoryMetadata($id, $className, $objectInstantiator->value(), $lifetime->code());
        $factoryMetadataMap->put($factoryMetadata->id, $factoryMetadata);

        try {
            $constructorValueMap = $this->constructorValueExtractor->extract($objectInstantiator);
        } catch (ArgumentTypeNotFoundException $e) {
            throw DefinitionTransformerException::createFromException(
                "An argument of [{$id}<{$className}>] constructor depend on class that not actually exists. Details: {$e->getMessage()}",
                $e
            );
        } catch (InvalidArgumentException $e) {
            throw DefinitionTransformerException::createFromException(
                "[{$id}<{$className}>] contains invalid constructor method. Details: {$e->getMessage()}",
                $e
            );
        } catch (ConstructorMetadataExtractorException $e) {
            throw DefinitionTransformerException::createFromException(
                "An error occurred, during argument metadata extraction from [{$id}<{$className}>]. Details: {$e->getMessage()}",
                $e
            );
        }

        $valueMap = $this->valueCollector->collect($definition, $definitionMap);
        $isComplete = true;
        /** @var \vinyl\di\definition\constructorMetadata\ConstructorValue $constructorValue */
        foreach ($constructorValueMap as $argumentName => $constructorValue) {
            $definitionValue = $valueMap->find($argumentName) ?? NoValue::get();

            try {
                $result = $this->valueProcessor->process($definitionValue, $constructorValue, $definitionMap);
            } catch (ValueProcessorException $e) {
                throw DefinitionTransformerException::createFromException(
                    "An error occurred during processing [{$argumentName}] argument of [{$id}] definition. Details: {$e->getMessage()}",
                    $e
                );
            }

            $factoryValue = $result->valueMetadata;

            if ($factoryValue->isMissed()) {
                $isComplete = false;
            }

            $factoryMetadata->values[$argumentName] = $factoryValue;

            if ($result->dependencies === null) {
                continue;
            }

            foreach ($result->dependencies as $dependency) {
                $classes = $visitedClasses;
                if (!$dependency->isPartOfDependencyGraph) {
                    $classes = [];
                }

                try {
                    $this->internalTransform($dependency->definition, $definitionMap, $factoryMetadataMap, $classes);
                } catch (DefinitionTransformerException $e) {
                    $e->add($id, $className, $argumentName);
                    throw $e;
                }
            }
        }

        $factoryMetadata->isComplete = $isComplete;

        #todo call definition post processor here ???

        if (!$isComplete && $lifetime === SingletonLifetime::get()) {
            throw DefinitionTransformerException::createIncompleteException($factoryMetadata);
        }
    }
}
