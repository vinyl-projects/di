<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition\value;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\MapValue;
use vinyl\di\definition\value\ArrayMapValue;
use vinyl\di\definition\value\Mergeable;

/**
 * Class ArrayMapValueTest
 */
class ArrayMapValueTest extends TestCase
{
    /**
     * @test
     */
    public function defaultValueIsEmptyArray(): void
    {
        $mapValue = $this->createValue();
        self::assertIsArray($mapValue->value());
        self::assertCount(0, $mapValue->value());
    }

    /**
     * @test
     */
    public function createFromConstructor(): void
    {
        $value = $this->createValueMock(10);

        $mapValue = $this->createValue(['some_key' => $value]);

        self::assertContains($value, $mapValue->value());
        self::assertArrayHasKey('some_key', $mapValue->value());
    }

    /**
     * @test
     */
    public function valueIsAvailableAfterPut(): void
    {
        $valueMock = $this->createValueMock(10);
        $mapValue = $this->createValue();

        $mapValue->put('key', $valueMock);

        self::assertContains($valueMock, $mapValue->value());
        self::assertArrayHasKey('key', $mapValue->value());
    }

    /**
     * @test
     */
    public function findByKeyReturnsNullIfNoSuchKey(): void
    {
        $mapValue = $this->createValue();

        self::assertNull($mapValue->findByKey('key'));
    }

    /**
     * @test
     */
    public function findByNameReturnsCorrectValueObject(): void
    {
        $mockObject = $this->createValueMock(10);
        $mapValue = $this->createValue(['key' => $mockObject]);

        self::assertSame($mockObject, $mapValue->findByKey('key'));
    }

    /**
     * @test
     */
    public function itemsAreClonedAfterCloningMap(): void
    {
        $item1 = $this->createValueMock(1);
        $item2 = $this->createValueMock(2);

        $mapValue = $this->createValue([$item1, $item2]);
        $clonedMap = clone $mapValue;

        self::assertNotSame($item1, $clonedMap->findByKey(0));
        self::assertNotSame($item2, $clonedMap->findByKey(1));
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfTryingToMergeWithNotMapValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var DefinitionValue&Mergeable $mergeableValueMock */
        $mergeableValueMock = new class implements DefinitionValue, Mergeable {
            public function value() { return null; }
            public function merge(Mergeable $mergeableValue): DefinitionValue { throw new \RuntimeException('Not implemented.'); }
        };
        $this->createValue()->merge($mergeableValueMock);
    }

    /**
     * @test
     */
    public function mergeReturnsNewArrayMapValue(): void
    {
        $mapValue1 = $this->createValue();
        $mapValue2 = $this->createValue();

        $newMapValue = $mapValue1->merge($mapValue2);

        self::assertNotSame($newMapValue, $mapValue1);
        self::assertNotSame($newMapValue, $mapValue2);
    }

    /**
     * @test
     */
    public function mergeReturnsMapWithNewValuesFromFirstMap(): void
    {
        $value1 = $this->createValueMock(1);
        $value2 = $this->createValueMock(2);
        $mapValue = $this->createValue(['value1' => $value1, 'value2' => $value2]);
        $mapValue2 = $this->createValue();

        /** @var MapValue $newMap */
        $newMap = $mapValue->merge($mapValue2);

        self::assertNotSame($newMap->findByKey('value1'), $value1);
        self::assertNotSame($newMap->findByKey('value2'), $value2);
        self::assertEquals($newMap->findByKey('value1')->value(), $value1->value());
        self::assertEquals($newMap->findByKey('value2')->value(), $value2->value());
    }

    /**
     * @test
     */
    public function mergeReturnsMapWithNewValuesFromSecondMap(): void
    {
        $value1 = $this->createValueMock(1);
        $value2 = $this->createValueMock(2);
        $mapValue = $this->createValue();
        $mapValue2 = $this->createValue(['value1' => $value1, 'value2' => $value2]);

        /** @var MapValue $newMap */
        $newMap = $mapValue->merge($mapValue2);

        self::assertNotSame($newMap->findByKey('value1'), $value1);
        self::assertNotSame($newMap->findByKey('value2'), $value2);
        self::assertEquals($newMap->findByKey('value1')->value(), $value1->value());
        self::assertEquals($newMap->findByKey('value2')->value(), $value2->value());
    }

    /**
     * @test
     */
    public function mergeReturnsMapWithNewValuesFromSecondMapIfKeysAreDuplicate(): void
    {
        $value1 = $this->createValueMock(1);
        $value2 = $this->createValueMock(2);
        $mapValue = $this->createValue(['value1' => $value1]);
        $mapValue2 = $this->createValue(['value1' => $value2]);

        /** @var MapValue $newMap */
        $newMap = $mapValue->merge($mapValue2);

        self::assertNotNull($newMap->findByKey('value1'));
        self::assertEquals(2, $newMap->findByKey('value1')->value());
        self::assertNotSame($newMap->findByKey('value1'), $value2);
    }

    /**
     * @test
     */
    public function mergeCallMergeOnValuesIfTheyAreMergeableAndHaveSameKey(): void
    {
        $value2 = $this->getMockBuilder(\vinyl\di\definition\MapValue::class)
            ->getMock();

        $value1 = $this->getMockBuilder(\vinyl\di\definition\MapValue::class)
            ->getMock();
        $value1->expects(self::once())->method('merge')->with($value2);

        $mapValue = $this->createValue(['key' => $value1]);
        $mapValue2 = $this->createValue(['key' => $value2]);

       $mapValue->merge($mapValue2);
    }

    /**
     * @param mixed $value
     *
     * @return MockObject&DefinitionValue
     */
    private function createValueMock($value): MockObject
    {
        $mock = $this->getMockBuilder(DefinitionValue::class)
            ->getMock();

        $mock->method('value')->willReturn($value);

        return $mock;
    }

    /**
     * {@inheritDoc}
     *
     * @return ArrayMapValue
     */
    private function createValue(array $items = []): MapValue
    {
        return new ArrayMapValue($items);
    }
}
