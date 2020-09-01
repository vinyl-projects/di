<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);


namespace vinyl\diTest\unit\metadataBuilder;

use PHPUnit\Framework\TestCase;
use vinyl\di\AliasDefinition;
use vinyl\di\AliasOnAliasDefinition;
use vinyl\di\ClassDefinition;
use vinyl\di\definition\ClassResolver;
use vinyl\di\definition\DefinitionCircularReferenceFoundException;
use vinyl\di\definition\RecursionFreeClassResolver;
use vinyl\std\lang\ClassObject;
use function get_class;
use function vinyl\std\lang\collections\mutableMapOf;
use function vinyl\std\lang\collections\pair;

/**
 * Class ClassResolverTest
 */
class ClassResolverTest extends TestCase
{
    protected ClassResolver $classResolver;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->classResolver = $this->createClassResolver();
    }

    /**
     * @test
     */
    public function resolveClassForDefinition(): void
    {
        $testClass = new class
        {
        };
        $definition = new ClassDefinition(ClassObject::create(get_class($testClass)));
        $config = mutableMapOf(pair($definition->id(), $definition));

        $resolve = $this->classResolver->resolve($definition, $config);

        self::assertEquals(get_class($testClass), $resolve->name());
    }

    /**
     * @test
     */
    public function resolveClassForDefinitionWhichNotDefinedInDefinitionMap(): void
    {
        $testClass = new class
        {
        };
        $definition = new ClassDefinition(ClassObject::create(get_class($testClass)));
        $resolve = $this->classResolver->resolve($definition, mutableMapOf());

        self::assertEquals(get_class($testClass), $resolve->name());
    }

    /**
     * @test
     */
    public function resolve(): void
    {
        $testClass = new class
        {
        };

        $type = new AliasOnAliasDefinition('some.id', 'parent.id');
        $type2 = new AliasDefinition('parent.id', ClassObject::create(get_class($testClass)));
        $config = mutableMapOf(pair($type->id(),$type), pair($type2->id(), $type2));

        $resolvedClass = $this->classResolver->resolve($type, $config);

        self::assertEquals(get_class($testClass), $resolvedClass->name());
    }

    /**
     * @test
     */
    public function circularDependencyDetection(): void
    {
        $this->expectException(DefinitionCircularReferenceFoundException::class);
        $type = new AliasOnAliasDefinition('qwerty1', 'qwerty2');
        $type2 = new AliasOnAliasDefinition('qwerty2', 'qwerty3');
        $type3 = new AliasOnAliasDefinition('qwerty3', 'qwerty1');
        $config = mutableMapOf(
            pair($type->id(), $type),
            pair($type2->id(), $type2),
            pair($type3->id(), $type3),
        );

        $this->classResolver->resolve($type, $config);
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenClassCantBeResolved(): void
    {
        $this->expectException(\vinyl\di\definition\ClassResolverException::class);
        $type = new AliasOnAliasDefinition('qwerty1', 'qwerty2');
        $type2 = new AliasOnAliasDefinition('qwerty2', 'qwerty3');
        $type3 = new AliasOnAliasDefinition('qwerty3', 'qwerty4');
        $config = mutableMapOf(
            pair($type->id(), $type),
            pair($type2->id(), $type2),
            pair($type3->id(), $type3),
        );

        $this->classResolver->resolve($type, $config);
    }

    protected function createClassResolver(): ClassResolver
    {
        return new RecursionFreeClassResolver();
    }
}
