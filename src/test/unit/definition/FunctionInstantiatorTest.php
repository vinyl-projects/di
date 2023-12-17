<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use vinyl\di\definition\FunctionInstantiator;
use function array_walk;

/**
 * Class FunctionInstantiatorTest
 */
final class FunctionInstantiatorTest extends TestCase
{
    /**
     * @test
     */
    public function exceptionIsThrownIfMethodNotExists(): void
    {
        $this->expectException(InvalidArgumentException::class);

        FunctionInstantiator::create('test');
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfFunctionReturnNotDeclared(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $functionName = 'vinyl\diTest\unit\definition\functionInstantiatorTestAsset\function_with_no_return_declared';
        $this->expectExceptionMessage("{$functionName} have no return type.");

        FunctionInstantiator::create($functionName);
    }

    /**
     * @test
     */
    public function createFromClosure(): void
    {
        $functionName = \vinyl\diTest\unit\definition\functionInstantiatorTestAsset\ClassA::test(...);
        $instantiator = FunctionInstantiator::createFromClosure($functionName);
        self::assertIsCallable($instantiator->value());
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfFunctionReturnVoid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $functionName = 'vinyl\diTest\unit\definition\functionInstantiatorTestAsset\function_with_void_return_declared';
        $this->expectExceptionMessage("{$functionName} have no return type.");

        FunctionInstantiator::create($functionName);
    }

    /**
     * @test
     */
    public function valueReturnsCallableString(): void
    {
        $value = FunctionInstantiator::create('vinyl\diTest\unit\definition\functionInstantiatorTestAsset\function_with_parameters')->value();
        self::assertIsCallable($value);
    }

    /**
     * @test
     */
    public function parametersReturnsAnArrayOfReflectionParameter(): void
    {
        $parameters = FunctionInstantiator::create('vinyl\diTest\unit\definition\functionInstantiatorTestAsset\function_with_parameters')->parameters();
        self::assertNotEmpty($parameters);

        array_walk(
            $parameters,
            static fn($parameter) => self::assertInstanceOf(ReflectionParameter::class, $parameter)
        );
    }
}
