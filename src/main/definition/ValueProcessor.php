<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\std\lang\collections\Map;

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
     * @param Map<string, \vinyl\di\Definition>                         $definitionMap
     *
     * @return \vinyl\di\definition\ValueProcessorResult
     * @throws \vinyl\di\definition\ValueProcessorException top level exception of <a href='psi_element://ValueProcessor'>ValueProcessor</a>
     * @throws \vinyl\di\definition\IncompatibleTypeException is thrown, if an incompatible type is specified as parameter
     * @throws \vinyl\di\definition\NullValueException is thrown, if null value provided for non nullable type
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        Map $definitionMap
    ): ValueProcessorResult;
}
