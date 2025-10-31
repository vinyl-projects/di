<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use vinyl\di\definition\StaticMethodInstantiator;
use function array_walk;
use function get_class;

/**
 * Class StaticMethodInstantiatorTest
 */
final class StaticMethodInstantiatorTest extends TestCase
{
    #[Test]
    public function exceptionIsThrownIfMethodNotStatic(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $mock = new class {
            public function test(): void
            {

            }
        };
        $className = get_class($mock);
        $this->expectExceptionMessage("{$className}::test not static.");

        new StaticMethodInstantiator($className, 'test');
    }

    #[Test]
    public function exceptionIsThrownIfStaticMethodNotPublic(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $mock = new class {
            private static function test(): void
            {

            }
        };
        $className = get_class($mock);
        $this->expectExceptionMessage("{$className}::test not public.");

        new StaticMethodInstantiator($className, 'test');
    }

    #[Test]
    public function exceptionIsThrownIfStaticMethodHaveNoReturnType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $mock = new class {
            public static function test()
            {

            }
        };

        $className = get_class($mock);
        $this->expectExceptionMessage("{$className}::test have no return type.");

        new StaticMethodInstantiator($className, 'test');
    }

    #[Test]
    public function exceptionIsThrownIfStaticMethodReturnVoid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $mock = new class {
            public static function test(): void
            {

            }
        };

        $className = get_class($mock);
        $this->expectExceptionMessage("{$className}::test have no return type.");

        new StaticMethodInstantiator($className, 'test');
    }

    #[Test]
    public function exceptionIsThrownIfGivenClassNotExists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaticMethodInstantiator('test', 'test');
    }

    #[Test]
    public function exceptionIsThrownIfMethodNotExists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $mock = new class {
        };

        new StaticMethodInstantiator(get_class($mock), 'test');
    }

    #[Test]
    public function valueReturnsCallableString(): void
    {
        $mock = new class {
            public static function test(): self
            {
            }
        };

        $instantiator = new StaticMethodInstantiator(get_class($mock), 'test');
        self::assertIsCallable($instantiator->value());
    }

    #[Test]
    public function parametersReturnsAnArrayOfReflectionParameter(): void
    {
        $mock = new class {
            public static function test(int $a, string $b): self
            {
            }
        };

        $instantiator = new StaticMethodInstantiator(get_class($mock), 'test');

        $parameters = $instantiator->parameters();
        self::assertNotEmpty($parameters);

        array_walk(
            $parameters,
            static fn($parameter) => self::assertInstanceOf(ReflectionParameter::class, $parameter)
        );
    }
}
