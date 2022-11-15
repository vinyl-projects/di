<?php

declare(strict_types=1);

namespace vinyl\di\factory\compiler;

use vinyl\di\factory\argument\ArrayValue;
use vinyl\di\factory\FactoryValue;
use function assert;
use function implode;
use function var_export;

/**
 * Class ArrayValueRenderer
 */
final class ArrayValueRenderer implements ValueRenderer
{
    private ValueRenderer $valueRenderer;

    /**
     * ArrayValueRenderer constructor.
     */
    public function __construct(ValueRenderer $valueRenderer)
    {
        $this->valueRenderer = $valueRenderer;
    }

    /**
     * {@inheritDoc}
     */
    public function render(FactoryValue $value): string
    {
        assert($value instanceof ArrayValue);

        if ($value->value() === null) {
            return 'null';
        }

        $renderedArrayItemList = [];
        foreach ($value->value() as $key => $arrayValue) {
            $renderedArrayItemList[] = var_export($key, true) . ' => ' . $this->valueRenderer->render($arrayValue);
        }

        return '[' . implode(',', $renderedArrayItemList) . ']';
    }
}
