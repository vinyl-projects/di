<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use vinyl\di\factory\FactoryValue;
use vinyl\std\lang\collections\Vector;

/**
 * Class ValueProcessorResult
 */
final class ValueProcessorResult
{
    public FactoryValue $valueMetadata;

    /** @var Vector<\vinyl\di\definition\DefinitionDependency>|null */
    public ?Vector $dependencies;

    /**
     * ValueProcessorResult constructor.
     *
     * @psalm-param Vector<\vinyl\di\definition\DefinitionDependency>|null $dependencies
     */
    public function __construct(
        FactoryValue $valueMetadata,
        ?Vector $dependencies = null
    ) {
        $this->valueMetadata = $valueMetadata;
        $this->dependencies = $dependencies;
    }
}
