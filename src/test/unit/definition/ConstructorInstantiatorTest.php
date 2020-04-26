<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition;

use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use vinyl\di\definition\ConstructorInstantiator;
use vinyl\diTest\unit\definition\constructorInstantiatorTestAsset\ClassWithPrivateConstructor;
use vinyl\std\lang\ClassObject;
use function array_walk;
use function get_class;

/**
 * Class ConstructorInstantiatorTest
 */
final class ConstructorInstantiatorTest extends TestCase
{
    /**
     * @test
     */
    public function exceptionIsThrownIfConstructorNotPublic(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $className = ClassWithPrivateConstructor::class;
        $this->expectExceptionMessage("Constructor method [{$className}::__construct] must not be private or protected.");

        new ConstructorInstantiator(ClassObject::create($className));
    }

    /**
     * @test
     */
    public function valueMethodReturnsNull(): void
    {
        $mock = new class {};

        self::assertNull((new ConstructorInstantiator(ClassObject::create(get_class($mock))))->value());
    }

    /**
     * @test
     */
    public function parametersMethodReturnsEmptyArrayIfClassHaveNoConstructor(): void
    {
        $mock = new class {};
        self::assertCount(0, (new ConstructorInstantiator(ClassObject::create(get_class($mock))))->parameters());
    }

    /**
     * @test
     */
    public function parametersReturnsAnArrayOfReflectionParameter(): void
    {
        $mock = new class(1, '') {
            public function __construct(int $a, string $b)
            {
            }
        };

        $parameters = (new ConstructorInstantiator(ClassObject::create(get_class($mock))))->parameters();
        array_walk(
            $parameters,
            static fn($parameter) => self::assertInstanceOf(ReflectionParameter::class, $parameter)
        );
    }
}
