<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition\value;

use PHPUnit\Framework\Attributes\Test;
use vinyl\di\definition\value\NoValue;
use PHPUnit\Framework\TestCase;

/**
 * Class NoValueTest
 */
final class NoValueTest extends TestCase
{
    #[Test]
    public function exceptionIsThrownIfValueMethodIsCalled(): void
    {
        $this->expectException(\LogicException::class);
        (new NoValue())->value();
    }

    #[Test]
    public function getMethodAlwaysReturnSameInstance(): void
    {
        self::assertSame(NoValue::get(), NoValue::get());
    }
}
