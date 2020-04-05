<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition\value;

use PHPUnit\Framework\TestCase;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\value\Mergeable;
use vinyl\di\definition\value\ObjectValue;
use vinyl\di\definition\value\ProxyValue;
use function get_class;

/**
 * Class ObjectValueTest
 */
abstract class AbstractObjectValueTest extends TestCase
{
    /**
     * @test
     */
    public function exceptionIsThrownIfEmptyDefinitionIdProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('definitionId could not be empty.');
        $this->createValue('');
    }

    /**
     * @test
     */
    public function correctValueIsReturned(): void
    {
        $objectValue = $this->createValue('some.id');
        self::assertEquals('some.id', $objectValue->value());
    }

    /**
     * test
     */
    public function exceptionIsThrownIfUnsuitableObjectPassedToMerge(): void
    {
        $object = new class implements DefinitionValue, Mergeable
        {
            public function merge(Mergeable $mergeableValue): DefinitionValue
            {
                throw new \RuntimeException('Not implemented.');
            }

            public function value()
            {
                throw new \RuntimeException('Not implemented.');
            }
        };

        $firstClass = ObjectValue::class;
        $objectClass = get_class($object);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot merge [$firstClass] with [$objectClass].");

        $this->createValue('some.id')->merge($object);
    }

    /**
     * test
     */
    public function newObjectValueReturnedAfterMergingWithObjectValue(): void
    {
        $firstObjectValue = $this->createValue('first');
        $secondObjectValue = $this->createValue('second');

        $merged = $firstObjectValue->merge($secondObjectValue);

        self::assertInstanceOf(ObjectValue::class, $merged);
        self::assertEquals('second', $merged->value());
        self::assertNotSame($secondObjectValue, $merged);
    }

    /**
     * test
     */
    public function newProxyValueReturnedAfterMergingWithProxyValue(): void
    {
        $objectValue = $this->createValue('first');
        $proxyValue = new ProxyValue('proxy.def.id');

        $merged = $objectValue->merge($proxyValue);
        self::assertInstanceOf(ProxyValue::class, $merged);
        self::assertNotSame($proxyValue, $merged);
        self::assertEquals('proxy.def.id', $merged->value());
    }

    /**
     * test
     */
    public function proxyValueWithObjectDefinitionIdIsReturnedAfterMergingWithProxyValueWhereDefinitionIdIsNull(): void
    {
        $objectValue = $this->createValue('first');
        $proxyValue = new ProxyValue(null);

        $merged = $objectValue->merge($proxyValue);
        self::assertInstanceOf(ProxyValue::class, $merged);
        self::assertNotSame($proxyValue, $merged);
        self::assertEquals('first', $merged->value());
    }

    /**
     * Returns new object value
     *
     * @param mixed  ...$additionalArguments
     *
     * @return \vinyl\di\definition\DefinitionValue&\vinyl\di\definition\value\Mergeable
     */
    abstract protected function createValue(string $definitionId, ... $additionalArguments): DefinitionValue;
}
