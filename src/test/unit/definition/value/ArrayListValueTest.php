<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition\value;

use vinyl\di\definition\ListValue;
use vinyl\di\definition\value\ArrayListValue;

/**
 * Class ListValueTest
 */
class ArrayListValueTest extends AbstractListValueTest
{
    /**
     * @test
     */
    public function createFromConstructor(): void
    {
        $value = $this->createValueMock(10);

        $listValue = $this->createValue([$value]);

        self::assertContains($value, $listValue->value());
    }

    /**
     * @test
     */
    public function defaultValueIsEmptyArray(): void
    {
        $listValue = $this->createValue();
        self::assertIsArray($listValue->value());
        self::assertCount(0, $listValue->value());
    }

    /**
     * {@inheritDoc}
     */
    protected function createValue(array $items = []): ListValue
    {
        return new ArrayListValue($items);
    }
}
