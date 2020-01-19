<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\definition\constructorMetadata\ConstructorValue;

/**
 * Interface ValueProcessor
 */
interface ValueProcessor
{
    /**
     * Processing definition argument value
     *
     * Pay attention that processor could be called for the same type twice and more
     *
     * @throws \vinyl\di\definition\ValueProcessorException top level exception of {@see ValueProcessor}
     * @throws \vinyl\di\definition\IncompatibleTypeException is thrown, if an incompatible type is specified as parameter
     * @throws \vinyl\di\definition\NullValueException is thrown, if null value provided for non nullable type
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        UnmodifiableDefinitionMap $definitionMap
    ): ValueProcessorResult;
}
