<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration\scoped;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use vinyl\di\Container;
use vinyl\di\ContainerException;
use vinyl\di\definition\PrototypeLifetime;
use vinyl\di\definition\ScopedLifetime;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\factory\DefinitionMapTransformer;
use vinyl\di\factory\FactoryMetadataMap;
use vinyl\di\LifetimeProvider;
use vinyl\di\NotFoundException;
use vinyl\di\ObjectFactory;
use vinyl\diTest\integration\testAsset\scoped\ServiceA;
use vinyl\diTest\integration\testAsset\scoped\ServiceB;

/**
 * Class AbstractScopedContainerTest
 */
abstract class AbstractScopedContainerTest extends TestCase
{
    abstract protected function createFactory(FactoryMetadataMap $classFactoryMetadataMap): ObjectFactory;

    /**
     * @test
     */
    public function exceptionIsThrownWhenScopedServiceInstantiatedFromRootContainer(): void
    {
        $this->expectException(ContainerException::class);

        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ServiceA::class)->lifetime(ScopedLifetime::get())->end()
            ;
            // @formatter:on
        });

        $di->get(ServiceA::class);
    }

    /**
     * @test
     */
    public function rootAndScopedContainerContainDifferentFactoryObject(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ServiceA::class)->lifetime(ScopedLifetime::get())->end()
            ;
            // @formatter:on
        });

        $scopedDi = $di->createScopedContainer();

        self::assertNotSame($di->get(ObjectFactory::class), $scopedDi->get(ObjectFactory::class));
    }

    /**
     * @test
     */
    public function rootAndScopedContainerContainDifferentContainerObject(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ServiceA::class)
                    ->lifetime(ScopedLifetime::get())
                ->end()
            ;
            // @formatter:on
        });

        $scopedDi = $di->createScopedContainer();

        self::assertNotSame($di->get(ContainerInterface::class), $scopedDi->get(ContainerInterface::class));
    }

    /**
     * @test
     */
    public function successfulInstantiationScopedServiceFromScopedContainer(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ServiceA::class)->lifetime(ScopedLifetime::get())->end()
            ;
            // @formatter:on
        });

        $scopedDi = $di->createScopedContainer();
        self::assertInstanceOf(ServiceA::class, $scopedDi->get(ServiceA::class));
    }

    /**
     * @test
     */
    public function scopedContainerReturnsAlwaysSameScopedService(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ServiceA::class)->lifetime(ScopedLifetime::get())->end()
            ;
            // @formatter:on
        });

        $scopedDi = $di->createScopedContainer();
        self::assertSame($scopedDi->get(ServiceA::class), $scopedDi->get(ServiceA::class));
    }

    /**
     * @test
     */
    public function successfulInstantiationScopedServiceWithSingletonServiceDependency(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ServiceA::class)->end()
                ->classDefinition(ServiceB::class)->lifetime(ScopedLifetime::get())->end()
            ;
            // @formatter:on
        });

        $scopedDi = $di->createScopedContainer();

        $service = $scopedDi->get(ServiceB::class);

        self::assertInstanceOf(ServiceB::class, $service);
        self::assertInstanceOf(ServiceA::class, $service->serviceA);
        self::assertSame($di->get(ServiceA::class), $service->serviceA);
    }

    /**
     * @test
     */
    public function successfulInstantiationScopedServiceWithPrototypeServiceDependency(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ServiceA::class)->lifetime(PrototypeLifetime::get())->end()
                ->classDefinition(ServiceB::class)->lifetime(ScopedLifetime::get())->end()
            ;
            // @formatter:on
        });

        $scopedDi = $di->createScopedContainer();

        $service = $scopedDi->get(ServiceB::class);

        self::assertInstanceOf(ServiceB::class, $service);
        self::assertInstanceOf(ServiceA::class, $service->serviceA);
        self::assertNotSame($di->get(ServiceA::class), $service->serviceA);
    }

    /**
     * @test
     * @testWith ["globalDi"]
     *           ["scopedDi"]
     */
    public function exceptionIsThrownDuringInstantiationSingletonServiceWithScopedDependency(string $diType): void
    {
        $this->expectException(ContainerException::class);

        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ServiceA::class)->lifetime(ScopedLifetime::get())->end()
                ->classDefinition(ServiceB::class)->end()
            ;
            // @formatter:on
        });

        if ($diType === 'globalDi') {
            $di->get(ServiceB::class);
        }

        if ($diType === 'scopedDi') {
            $scopedDi = $di->createScopedContainer();
            $scopedDi->get(ServiceB::class);
        }
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenNotRegisteredServiceIsRequested(): void
    {
        $this->expectException(NotFoundException::class);

        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
        });

        $di->createScopedContainer()->get('SomeService');
    }

    /**
     * @test
     * @testWith    ["vinyl\\diTest\\integration\\testAsset\\scoped\\ServiceA", true]
     *              ["SomeService", false]
     */
    public function checksServiceAvailabilityInContainer(string $service, bool $availability): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ServiceA::class)->end()
            ;
            // @formatter:on
        });

        self::assertEquals($availability, $di->createScopedContainer()->has($service));
    }

    /**
     * Returns di container
     */
    protected function createContainer(callable $builderFunction): Container
    {
        $metadataBuilder = new DefinitionMapTransformer();
        $definitionMapBuilder = new DefinitionMapBuilder();
        $builderFunction($definitionMapBuilder);
        $definitionMap = $definitionMapBuilder->build();
        $classFactoryMetadataMap = $metadataBuilder->transform($definitionMap);

        return new Container(
            new LifetimeProvider($definitionMap->toLifetimeArrayMap()),
            $this->createFactory($classFactoryMetadataMap)
        );
    }
}
