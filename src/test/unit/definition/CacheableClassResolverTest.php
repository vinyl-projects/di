<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\definition;

use PHPUnit\Framework\TestCase;
use vinyl\di\Definition;
use vinyl\di\definition\CacheableClassResolver;
use vinyl\di\definition\ClassResolver;
use vinyl\di\definition\DefinitionMap;
use vinyl\std\ClassObject;
use function get_class;

/**
 * Class CacheableClassResolverTest
 */
final class CacheableClassResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolveMethodReturnsResolvedClassObject(): void
    {
        $classMock = new class {};
        $classObject = ClassObject::create(get_class($classMock));
        $definitionMap = new DefinitionMap([]);
        $definition = $this->createMock(Definition::class);
        $classResolverMock = $this->createMock(ClassResolver::class);
        $classResolverMock->expects(self::once())
            ->method('resolve')
            ->with($definition, $definitionMap)
            ->willReturn($classObject);
        $cacheableClassResolver = new CacheableClassResolver($classResolverMock);

        $resolvedClassObject = $cacheableClassResolver->resolve($definition, $definitionMap);
        self::assertSame($classObject, $resolvedClassObject);
    }

    /**
     * @test
     */
    public function secondCallOfResolveMethodWillReturnsResultFromCache(): void
    {
        $classMock = new class {};
        $classObject = ClassObject::create(get_class($classMock));
        $definitionMap = new DefinitionMap([]);
        $definition = $this->createMock(Definition::class);
        $classResolverMock = $this->createMock(ClassResolver::class);

        $classResolverMock->expects(self::once())
            ->method('resolve')
            ->with($definition, $definitionMap)
            ->willReturn($classObject);
        $cacheableClassResolver = new CacheableClassResolver($classResolverMock);

        $cacheableClassResolver->resolve($definition, $definitionMap);
        $resolvedClassObject= $cacheableClassResolver->resolve($definition, $definitionMap);

        self::assertSame($classObject, $resolvedClassObject);
    }

    /**
     * @test
     */
    public function internalCacheIsResetIfDifferentDefinitionMapIsProvided(): void
    {
        $classMock = new class {};
        $classObject = ClassObject::create(get_class($classMock));
        $definition = $this->createMock(Definition::class);
        $classResolverMock = $this->createMock(ClassResolver::class);

        $classResolverMock->expects(self::exactly(2))
            ->method('resolve')
            ->withAnyParameters()
            ->willReturn($classObject);
        $cacheableClassResolver = new CacheableClassResolver($classResolverMock);

        $cacheableClassResolver->resolve($definition, new DefinitionMap([]));
        $resolvedClassObject= $cacheableClassResolver->resolve($definition, new DefinitionMap([]));

        self::assertSame($classObject, $resolvedClassObject);
    }

    /**
     * @test
     */
    public function cleanCacheMethodCleansResolvedClassCache(): void
    {
        $classMock = new class {};
        $classObject = ClassObject::create(get_class($classMock));
        $definition = $this->createMock(Definition::class);
        $classResolverMock = $this->createMock(ClassResolver::class);
        $classResolverMock->expects(self::exactly(2))
            ->method('resolve')
            ->withAnyParameters()
            ->willReturn($classObject);
        $cacheableClassResolver = new CacheableClassResolver($classResolverMock);

        $definitionMap = new DefinitionMap([]);
        $cacheableClassResolver->resolve($definition, $definitionMap);
        $cacheableClassResolver->cleanCache();
        $resolvedClassObject= $cacheableClassResolver->resolve($definition, $definitionMap);

        self::assertSame($classObject, $resolvedClassObject);
    }
}
