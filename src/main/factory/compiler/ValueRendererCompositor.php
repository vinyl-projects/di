<?php

declare(strict_types=1);

namespace vinyl\di\factory\compiler;

use vinyl\di\factory\argument\ArrayValue;
use vinyl\di\factory\argument\BuiltinFactoryValue;
use vinyl\di\factory\argument\DefinitionFactoryValue;
use vinyl\di\factory\argument\EnumFactoryValue;
use vinyl\di\factory\FactoryValue;
use function array_key_exists;
use function array_replace;
use function get_class;

/**
 * Class ValueRendererCompositor
 */
final class ValueRendererCompositor implements ValueRenderer
{
    /** @var array<string, ValueRenderer> */
    private array $valueRendererMap;

    /**
     * ValueRendererCompositor constructor.
     *
     * @param array<string, \vinyl\di\factory\compiler\ValueRenderer> $valueRendererMap
     */
    public function __construct(?array $valueRendererMap = null)
    {
        $defaultValueRendererMap = [
            EnumFactoryValue::class       => new EnumValueRenderer(),
            DefinitionFactoryValue::class => new DefinitionValueRenderer(),
            BuiltinFactoryValue::class    => new ScalarValueRenderer(),
            ArrayValue::class             => new ArrayValueRenderer($this),//TODO Create ValueRendererAware interface
        ];

        if ($valueRendererMap !== null) {
            $defaultValueRendererMap = array_replace($defaultValueRendererMap, $valueRendererMap);
        }

        $this->valueRendererMap = $defaultValueRendererMap;
    }

    /**
     * {@inheritDoc}
     */
    public function render(FactoryValue $value): string
    {
        $valueClass = get_class($value);
        if (array_key_exists($valueClass, $this->valueRendererMap)) {
            return $this->valueRendererMap[$valueClass]->render($value);
        }

        throw new \RuntimeException("[{$valueClass}] Unsupported value type.");
    }
}
