<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition\value;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use vinyl\di\definition\ProxyValue;

/**
 * Class ProxyValueTest
 */
class ProxyValueTest extends TestCase
{
    /**
     * @test
     */
    public function exceptionIsThrownIfEmptyDefinitionIdProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->createValue('');
    }

    /**
     * @test
     */
    public function correctValueReturned(): void
    {
        self::assertEquals('test', $this->createValue('test')->value());
    }

    protected function createValue(?string $value, ...$otherArguments): ProxyValue
    {
        return new \vinyl\di\definition\value\ProxyValue($value);
    }
}
