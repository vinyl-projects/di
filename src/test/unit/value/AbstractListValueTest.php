<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\value;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\ListValue;
use vinyl\di\definition\OrderableValue;
use vinyl\di\definition\value\Mergeable;

/**
 * Class ListValueTest
 */
abstract class AbstractListValueTest extends TestCase
{
    /**
     * @test
     */
    public function addedItemsAreAvailableInValue(): void
    {
        $value1 = $this->createOrderableMock(1);
        $value2 = $this->createOrderableMock(2);
        $listValue = $this->createValue();

        $listValue->add($value1);
        $listValue->add($value2);

        self::assertContains($value1, $listValue->value());
        self::assertContains($value2, $listValue->value());
    }

    /**
     * @test
     */
    public function allListItemsAreClonedAfterCloningList(): void
    {
        $value1 = $this->createOrderableMock(1);
        $value2 = $this->createOrderableMock(2);
        $listValue = $this->createValue();

        $listValue->add($value1);
        $listValue->add($value2);

        $clonedList = clone $listValue;

        self::assertNotContains($value1, $clonedList->value());
        self::assertNotContains($value2, $clonedList->value());
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfMergingWithNotListValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $value = $this->createValue();

        /** @var MockObject&Mergeable $notListValue */
        $notListValue = $this->getMockBuilder(Mergeable::class)->getMock();
        $value->merge($notListValue);
    }

    /**
     * @test
     */
    public function valuesFromBothListsAvailableAfterMerge(): void
    {
        $value1 = $this->createOrderableMock(1);
        $value2 = $this->createOrderableMock(2);
        $value3 = $this->createOrderableMock(3);
        $value4 = $this->createOrderableMock(4);

        $firstList = $this->createValue([$value1, $value2]);
        $secondList = $this->createValue([$value3, $value4]);

        /** @var \vinyl\di\definition\ListValue $mergedList */
        $mergedList = $firstList->merge($secondList);

        self::assertTrue(self::listContainValue($mergedList, $value1));
        self::assertTrue(self::listContainValue($mergedList, $value2));
        self::assertTrue(self::listContainValue($mergedList, $value3));
        self::assertTrue(self::listContainValue($mergedList, $value4));
    }

    /**
     * @test
     */
    public function allValuesOfListAreClonedAfterMerging(): void
    {
        $value1 = $this->createOrderableMock(1);
        $value2 = $this->createOrderableMock(2);
        $value3 = $this->createOrderableMock(3);
        $value4 = $this->createOrderableMock(4);

        $firstList = $this->createValue([$value1, $value2]);
        $secondList = $this->createValue([$value3, $value4]);

        /** @var \vinyl\di\definition\ListValue $mergedList */
        $mergedList = $firstList->merge($secondList);

        self::assertNotContains($value1, $mergedList->value());
        self::assertNotContains($value2, $mergedList->value());
        self::assertNotContains($value3, $mergedList->value());
        self::assertNotContains($value4, $mergedList->value());
    }

    /**
     * @test
     */
    public function mergeReturnsNewListValue(): void
    {
        $firstList = $this->createValue();
        $secondList = $this->createValue();

        /** @var \vinyl\di\definition\ListValue $mergedList */
        $mergedList = $firstList->merge($secondList);

        self::assertNotSame($firstList, $mergedList);
        self::assertNotSame($secondList, $mergedList);
    }

    /**
     * @test
     */
    public function sort(): void
    {
        $value1 = $this->createOrderableMock(1, 100);
        $value2 = $this->createOrderableMock(2, 50);
        $value3 = $this->createOrderableMock(3);
        $value4 = $this->createOrderableMock(4, -50);

        $items = [$value1, $value2, $value3, $value4];
        $listValue = $this->createValue($items);

        $listValue->sort();

        $expectedValueOrders = [-50, 0, 50, 100];

        /** @var \vinyl\di\definition\OrderableValue $item */
        foreach ($listValue->value() as $index => $item) {
            self::assertEquals($expectedValueOrders[$index], $item->order());
        }
    }

    /**
     * @param mixed $value
     *
     * @return MockObject&OrderableValue
     */
    protected function createOrderableMock($value, int $order = 0): MockObject
    {
        $mock = $this->getMockBuilder(OrderableValue::class)
            ->onlyMethods(['value', 'order'])
            ->getMock();

        $mock->method('value')->willReturn($value);
        $mock->method('order')->willReturn($order);

        return $mock;
    }

    protected static function listContainValue(ListValue $listValue, DefinitionValue $value): bool
    {
        /** @var DefinitionValue $item */
        foreach ($listValue->value() as $item) {
            if ($value->value() === $item->value()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns new list value
     *
     * @param \vinyl\di\definition\OrderableValue[] $items
     */
    abstract protected function createValue(array $items = []): ListValue;
}
