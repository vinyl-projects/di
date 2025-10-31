<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition\value;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use vinyl\di\definition\DefinitionValue;

/**
 * Class ObjectValueTest
 */
abstract class AbstractObjectValueTestCase extends TestCase
{
    #[Test]
    public function exceptionIsThrownIfEmptyDefinitionIdProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('definitionId could not be empty.');
        $this->createValue('');
    }

    #[Test]
    public function correctValueIsReturned(): void
    {
        $objectValue = $this->createValue('some.id');
        self::assertEquals('some.id', $objectValue->value());
    }

    /**
     * Returns new object value
     *
     * @param mixed  ...$additionalArguments
     *
     * @return \vinyl\di\definition\DefinitionValue
     */
    abstract protected function createValue(string $definitionId, ... $additionalArguments): DefinitionValue;
}
