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

        new FunctionInstantiator('test');
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfFunctionReturnNotDeclared(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $functionName = 'vinyl\diTest\unit\definition\functionInstantiatorTestAsset\function_with_no_return_declared';
        $this->expectExceptionMessage("{$functionName} have no return type.");

        new FunctionInstantiator($functionName);
    }

    /**
     * @test
     */
    public function exceptionIsThrownIfFunctionReturnVoid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $functionName = 'vinyl\diTest\unit\definition\functionInstantiatorTestAsset\function_with_void_return_declared';
        $this->expectExceptionMessage("{$functionName} have no return type.");

        new FunctionInstantiator($functionName);
    }

    /**
     * @test
     */
    public function valueReturnsCallableString(): void
    {
        $value = (new FunctionInstantiator('vinyl\diTest\unit\definition\functionInstantiatorTestAsset\function_with_parameters'))->value();
        self::assertIsCallable($value);
    }

    /**
     * @test
     */
    public function parametersReturnsAnArrayOfReflectionParameter(): void
    {
        $parameters = (new FunctionInstantiator('vinyl\diTest\unit\definition\functionInstantiatorTestAsset\function_with_parameters'))->parameters();
        self::assertNotEmpty($parameters);

        array_walk(
            $parameters,
            static fn($parameter) => self::assertInstanceOf(ReflectionParameter::class, $parameter)
        );
    }
}
