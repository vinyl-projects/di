<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use vinyl\di\definition\StaticMethodInstantiator;
use function array_map;
use function get_class;

/**
 * Class StaticMethodInstantiatorTest
 */
final class StaticMethodInstantiatorTest extends TestCase
{
    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function exceptionIsThrownIfGivenClassNotExists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StaticMethodInstantiator('test', 'test');
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfMethodNotExists(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $mock = new class {
        };

        new StaticMethodInstantiator(get_class($mock), 'test');
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function parametersReturnsAnArrayOfReflectionParameter(): void
    {
        $mock = new class {
            public static function test(int $a, string $b): self
            {
            }
        };

        $instantiator = new StaticMethodInstantiator(get_class($mock), 'test');

        self::assertNotEmpty($instantiator->parameters());

        array_map(
            fn($parameter) => self::assertInstanceOf(ReflectionParameter::class, $parameter),
            $instantiator->parameters()
        );
    }
}
