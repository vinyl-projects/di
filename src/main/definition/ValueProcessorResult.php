<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\factory\FactoryValue;

/**
 * Class ValueProcessorResult
 */
final class ValueProcessorResult
{
    public FactoryValue $valueMetadata;
    public ?DefinitionToDependencyMap $definitionToDependencyMap;

    /**
     * ValueProcessorResult constructor.
     */
    public function __construct(
        FactoryValue $valueMetadata,
        ?DefinitionToDependencyMap $definitionToDependencyMap = null
    ) {
        $this->valueMetadata = $valueMetadata;
        $this->definitionToDependencyMap = $definitionToDependencyMap;
    }
}
