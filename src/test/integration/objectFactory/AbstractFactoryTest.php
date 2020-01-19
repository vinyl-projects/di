<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory;

use PHPUnit\Framework\TestCase;
use stdClass;
use vinyl\di\definition\PrototypeLifetime;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\factory\DefinitionMapTransformer;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\NotFoundException;
use vinyl\di\ObjectFactory;
use vinyl\diTest\integration\objectFactory\testAsset\instantiateObjectWithNullableArgument\ClassA;

/**
 * Class AbstractFactoryTest
 */
abstract class AbstractFactoryTest extends TestCase
{
    /**
     * @param array<string|int, int> $lifetimeArrayMap
     */
    abstract public function createFactory(
        FactoryMetadataMap $classFactoryMetadataMap,
        array $lifetimeArrayMap
    ): ObjectFactory;

    /**
     * @test
     */
    public function instantiateAlwaysNewObjectWithoutArguments(): void
    {
        $factory = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(stdClass::class)->end()
            ;
            // @formatter:on
        });

        $obj1 = $factory->create(stdClass::class);
        $obj2 = $factory->create(stdClass::class);

        self::assertNotSame($obj1, $obj2);
    }

    /**
     * @test
     */
    public function instantiateObjectWithProvidedArguments(): void
    {
        $factory = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\instantiateObjectWithProvidedArguments\ClassA::class)
                    ->arguments()
                        ->stringArgument('param1', 'test param')
                        ->intArgument('param2', 42)
                        ->arrayMapArgument('param3')
                            ->objectItem('someObject', stdClass::class)
                        ->end()
                    ->endArguments()
                ->end()
            ;
            // @formatter:on
        });

        $withoutArgumentsObject = new stdClass();

        $obj = $factory->create(testAsset\instantiateObjectWithProvidedArguments\ClassA::class, [
            'param1' => 'new value',
            'param2' => 24,
            'param3' => [],
            'param4' => $withoutArgumentsObject,
        ]);

        self::assertEquals('new value', $obj->param1);
        self::assertEquals(24, $obj->param2);

        self::assertArrayNotHasKey('someObject', $obj->param3);
        self::assertEquals([], $obj->param3);
        self::assertInstanceOf(stdClass::class, $obj->param4);
        self::assertSame($withoutArgumentsObject, $obj->param4);
    }

    /**
     * @test
     */
    public function requiredDefinitionNotRegisteredInDi(): void
    {
        $this->expectException(NotFoundException::class);
        $factory = $this->createContainer();
        $factory->create(stdClass::class);
    }

    /**
     * @test
     */
    public function instantiateObjectWithNullableArgument(): void
    {
        $factory = $this->createContainer(static function (DefinitionMapBuilder $definitionMapBuilder): void {
            $definitionMapBuilder
                ->classDefinition(ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end();
        });

        $obj = $factory->create(ClassA::class, ['data' => null]);
        self::assertNull($obj->data);
    }

    /**
     * Returns di container
     *
     * @param callable $builderFunction (containerBuilder)
     * @throws \vinyl\di\definition\DefinitionTransformerException
     */
    protected function createContainer(?callable $builderFunction = null): ObjectFactory
    {
        $metadataBuilder = new DefinitionMapTransformer();
        $definitionMapBuilder = new DefinitionMapBuilder();
        if ($builderFunction !== null) {
            $builderFunction($definitionMapBuilder);
        }

        $definitionMap = $definitionMapBuilder->build();

        return $this->createFactory($metadataBuilder->transform($definitionMap), $definitionMap->toLifetimeArrayMap());
    }
}
