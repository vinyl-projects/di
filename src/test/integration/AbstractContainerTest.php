<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration;

use PHPUnit\Framework\TestCase;
use ProxyManager\Proxy\VirtualProxyInterface;
use stdClass;
use vinyl\di\definition\ClassCircularReferenceFoundException;
use vinyl\di\definition\DefinitionTransformerException;
use vinyl\di\definition\FunctionInstantiator;
use vinyl\di\definition\PrototypeLifetime;
use vinyl\di\definition\StaticMethodInstantiator;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\NotEnoughArgumentsPassedException;
use vinyl\di\NotFoundException;
use vinyl\diTest\integration\testAsset\aliasDefinition\argumentInheritance\ClassA;
use vinyl\diTest\integration\testAsset\aliasDefinition\argumentInheritance\ClassB;
use vinyl\diTest\integration\testAsset\aliasDefinition\argumentInheritance\ClassC;
use vinyl\diTest\integration\testAsset\circularDependencyDetectionWithInterface\ClassAInterface;
use function array_values;

/**
 * Class AbstractContainerTest
 */
abstract class AbstractContainerTest extends TestCase
{
    /**
     * @test
     */
    public function instantiateObjectWithoutArguments(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            $containerBuilder->classDefinition(testAsset\instantiateObjectWithoutArguments\ClassA::class)->end();
        });

        self::assertInstanceOf(testAsset\instantiateObjectWithoutArguments\ClassA::class, $di->get(testAsset\instantiateObjectWithoutArguments\ClassA::class));
    }

    /**
     * @test
     */
    public function instantiateObjectWithArguments(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\instantiateObjectWithArguments\ClassA::class)
                    ->arguments()
                        ->intArgument('a', 42)
                        ->intArgument('b', null)
                        ->floatArgument('c', 42.5)
                        ->floatArgument('d', null)
                        ->boolArgument('e', false)
                        ->boolArgument('f', null)
                        ->stringArgument('g', 'hello world')
                        ->stringArgument('h', null)
                        ->objectArgument('i', stdClass::class)
                        ->objectArgument('j', null)
                        ->arrayMapArgument('k')
                            ->boolItem('a', true)
                            ->intItem('b', 42)
                            ->floatItem('c', 42.5)
                            ->stringItem('d', 'hello world')
                            ->objectItem('e', stdClass::class)
                        ->end()
                        ->arrayNullArgument('l')
                        ->arrayListArgument('m')
                            ->boolItem(true)
                            ->intItem(42)
                            ->floatItem(42.5)
                            ->stringItem('hello world')
                            ->objectItem(stdClass::class)
                        ->end()
                        ->arrayNullArgument('n')
                        ->objectArgument('p', null)
                        ->objectArgument('q', stdClass::class)
                        ->objectArgument('variadic', stdClass::class)
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $obj = $di->get(testAsset\instantiateObjectWithArguments\ClassA::class);
        self::assertInstanceOf(testAsset\instantiateObjectWithArguments\ClassA::class, $obj);
        self::assertEquals(42, $obj->a);
        self::assertNull($obj->b);
        self::assertEquals(42.5, $obj->c);
        self::assertNull($obj->d);
        self::assertFalse($obj->e);
        self::assertNull($obj->f);
        self::assertEquals('hello world', $obj->g);
        self::assertNull($obj->h);
        self::assertInstanceOf(stdClass::class, $obj->i);
        self::assertNull($obj->j);
        $kMap = [
            'a' => true,
            'b' => 42,
            'c' => 42.5,
            'd' => 'hello world',
            'e' => $obj->i
        ];
        self::assertSame($kMap, $obj->k);
        self::assertNull($obj->l);
        $mList = [true, 42, 42.5, 'hello world', $obj->i];
        self::assertSame($mList, $obj->m);
        self::assertNull($obj->n);
        self::assertInstanceOf(testAsset\instantiateObjectWithArguments\ClassB::class, $obj->o);
        self::assertInstanceOf(testAsset\instantiateObjectWithArguments\ClassB::class, $obj->ppp);
        self::assertNull($obj->p);
        self::assertInstanceOf(stdClass::class, $obj->q);
        self::assertInstanceOf(stdClass::class, $obj->variadic[0]);
        self::assertEquals(42, $obj->aa);
        self::assertNull($obj->bb);
        self::assertEquals(42.5, $obj->cc);
        self::assertNull($obj->dd);
        self::assertTrue($obj->ee);
        self::assertNull($obj->ff);
        self::assertEquals('hello world', $obj->gg);
        self::assertNull($obj->hh);
        self::assertNull($obj->jj);
        self::assertSame([1,2,3], $obj->kk);
        self::assertNull($obj->ll);
        self::assertNull($obj->pp);
    }

    /**
     * @test
     */
    public function alwaysReturnSameObject(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\alwaysReturnSameObject\ClassA::class)->end();
            // @formatter:on
        });

        $firstCall = $di->get(testAsset\alwaysReturnSameObject\ClassA::class);
        $secondCall = $di->get(testAsset\alwaysReturnSameObject\ClassA::class);

        self::assertSame($firstCall, $secondCall);
    }

    /**
     * @test
     */
    public function alwaysReturnNewObject(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\alwaysReturnNewObject\ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end();
            // @formatter:on
        });

        $firstCall = $di->get(testAsset\alwaysReturnNewObject\ClassA::class);
        $secondCall = $di->get(testAsset\alwaysReturnNewObject\ClassA::class);

        self::assertNotSame($firstCall, $secondCall);
    }

    /**
     * @test
     */
    public function instantiateInterfaceImplementation(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->interfaceImplementation(
                    testAsset\instantiateInterfaceImplementation\InterfaceA::class,
                    testAsset\instantiateInterfaceImplementation\ClassA::class
                );
            // @formatter:on
        });

        $obj = $di->get(testAsset\instantiateInterfaceImplementation\InterfaceA::class);

        self::assertInstanceOf(testAsset\instantiateInterfaceImplementation\InterfaceA::class, $obj);
        self::assertInstanceOf(testAsset\instantiateInterfaceImplementation\ClassA::class, $obj);
    }

    /**
     * @test
     */
    public function instantiateOverwrittenInterfaceImplementation(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->interfaceImplementation(
                    testAsset\instantiateInterfaceImplementation\InterfaceA::class,
                    testAsset\instantiateInterfaceImplementation\ClassA::class
                )
                ->classDefinition(testAsset\instantiateInterfaceImplementation\ClassA::class)
                    ->replaceClass(testAsset\instantiateInterfaceImplementation\ClassB::class)
                ->end();
            // @formatter:on
        });

        $obj = $di->get(testAsset\instantiateInterfaceImplementation\InterfaceA::class);

        self::assertInstanceOf(testAsset\instantiateInterfaceImplementation\InterfaceA::class, $obj);
        self::assertInstanceOf(testAsset\instantiateInterfaceImplementation\ClassB::class, $obj);
    }

    /**
     * @test
     */
    public function replaceClassNotOverrideArguments(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->interfaceImplementation(abstractContainerTest\preferenceNotOverrideArguments\A::class, abstractContainerTest\preferenceNotOverrideArguments\B::class)
                ->classDefinition(abstractContainerTest\preferenceNotOverrideArguments\B::class)
                    ->replaceClass(abstractContainerTest\preferenceNotOverrideArguments\C::class)
                    ->arguments()
                        ->stringArgument('name', 'Name B')
                        ->stringArgument('surname', 'Surname B')
                    ->endArguments()
                ->end()
                ->classDefinition(abstractContainerTest\preferenceNotOverrideArguments\C::class)
                    ->arguments()
                        ->stringArgument('name', 'Name C')
                        ->stringArgument('surname', 'Surname C')
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $obj = $di->get(abstractContainerTest\preferenceNotOverrideArguments\A::class);
        self::assertEquals('Preference - Name B', $obj->name());
        self::assertEquals('Preference - Surname B', $obj->surname());
        self::assertInstanceOf(
            abstractContainerTest\preferenceNotOverrideArguments\C::class,
            $obj
        );
    }

    /**
     * @test
     */
    public function interfaceUsedAsArgument(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->interfaceImplementation(testAsset\interfaceUsedAsArgument\InterfaceA::class, testAsset\interfaceUsedAsArgument\ClassA::class)
                ->classDefinition(testAsset\interfaceUsedAsArgument\ClassB::class)->end();
            // @formatter:on
        });

        $obj = $di->get(testAsset\interfaceUsedAsArgument\ClassB::class);

        self::assertInstanceOf(testAsset\interfaceUsedAsArgument\ClassA::class, $obj->interfaceA);
    }

    /**
     * @test
     */
    public function optionalInterfaceUsedAsArgument(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\optionalInterfaceUsedAsArgument\ClassA::class)->end();
            // @formatter:on
        });

        $obj = $di->get(testAsset\optionalInterfaceUsedAsArgument\ClassA::class);

        self::assertNull($obj->interfaceB);
    }

    /**
     * @test
     */
    public function circularDependencyDetection(): void
    {
        $this->expectException(ClassCircularReferenceFoundException::class);

        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(abstractContainerTest\circularDependencyDetection\ClassA::class)->end();
            // @formatter:on
        });

        $di->get(abstractContainerTest\circularDependencyDetection\ClassA::class);
    }

    /**
     * @test
     */
    public function circularDependencyDetectionWithInterface(): void
    {
        $this->expectException(ClassCircularReferenceFoundException::class);
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->interfaceImplementation(
                    ClassAInterface::class,
                    testAsset\circularDependencyDetectionWithInterface\ClassA::class
                )
                ->classDefinition(testAsset\circularDependencyDetectionWithInterface\ClassA::class)->end();
            // @formatter:on
        });

        $di->get(testAsset\circularDependencyDetectionWithInterface\ClassA::class);
    }

    /**
     * @test
     */
    public function circularDependencyResolvedByProxyUsage(): void
    {
        $di = $this->createContainer(
            static function (DefinitionMapBuilder $containerBuilder): void {
                // @formatter:off
                $containerBuilder
                    ->classDefinition(abstractContainerTest\circularDependencyDetection\ClassA::class)->end()

                    ->classDefinition(abstractContainerTest\circularDependencyDetection\ClassD::class)
                        ->arguments()
                            ->proxyArgument('classA', abstractContainerTest\circularDependencyDetection\ClassA::class)
                        ->endArguments()
                    ->end();
                // @formatter:on
            }
        );

        $obj = $di->get(abstractContainerTest\circularDependencyDetection\ClassA::class);
        self::assertInstanceOf(abstractContainerTest\circularDependencyDetection\ClassA::class, $obj);
        self::assertInstanceOf(
            'vinyl\diTest\integration\abstractContainerTest\circularDependencyDetection\ClassA\ClassA_AutoGeneratedProxy',
            $obj->classB->classC->classD->classA
        );
    }

    /**
     * @test
     */
    public function proxiedObjectInjectsItself(): void
    {
        $di = $this->createContainer(
            static function (DefinitionMapBuilder $containerBuilder): void {
                // @formatter:off
                $containerBuilder
                    ->classDefinition(testAsset\proxiedObjectInjectsItself\ClassA::class)
                        ->arguments()
                            ->proxyArgument('classA', null)
                        ->endArguments()
                    ->end();
                // @formatter:on
            }
        );

        $obj = $di->get(testAsset\proxiedObjectInjectsItself\ClassA::class);
        self::assertInstanceOf(VirtualProxyInterface::class, $obj->classA);

    }

    /**
     * @test
     */
    public function instantiateObjectWithInheritedArguments(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\instantiateObjectWithInheritedArguments\ClassD::class)
                    ->arguments()
                        ->stringArgument('name','john')
                        ->stringArgument('nickname', 'qwerty')
                        ->stringArgument('street', 'wasd')
                    ->endArguments()
                ->end()
                ->classDefinition(testAsset\instantiateObjectWithInheritedArguments\ClassC::class)
                    ->inheritArguments(true)
                    ->arguments()
                        ->stringArgument('surname', 'doe')
                    ->endArguments()
                ->end()
                ->classDefinition(testAsset\instantiateObjectWithInheritedArguments\ClassB::class)
                    ->inheritArguments(true)
                    ->arguments()
                        ->intArgument('age',42)
                        ->stringArgument('street','dasw')
                    ->endArguments()
                ->end()
                ->classDefinition(testAsset\instantiateObjectWithInheritedArguments\ClassA::class)
                    ->inheritArguments(true)
                    ->arguments()
                        ->stringArgument('nickname','ytrewq')
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $obj = $di->get(testAsset\instantiateObjectWithInheritedArguments\ClassA::class);

        self::assertEquals('john', $obj->name);
        self::assertEquals('doe', $obj->surname);
        self::assertEquals(42, $obj->age);
        self::assertEquals('ytrewq', $obj->nickname);
        self::assertEquals('dasw', $obj->street);
    }

    /**
     * @test
     */
    public function instantiateAliasAsClassArgument(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->alias('1some.virtual.type', testAsset\instantiateAliasAsClassArgument\ClassA::class)
                    ->arguments()
                        ->stringArgument('someData', 'hello')
                    ->endArguments()
                ->end()
                ->classDefinition(testAsset\instantiateAliasAsClassArgument\ClassB::class)
                    ->arguments()
                        ->objectArgument('classA', '1some.virtual.type')
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $object = $di->get(testAsset\instantiateAliasAsClassArgument\ClassB::class);
        self::assertInstanceOf(testAsset\instantiateAliasAsClassArgument\ClassA::class, $object->classA);
        self::assertSame('hello', $object->classA->someData);
    }

    /**
     * @test
     */
    public function instantiateAliasTypeWithArguments(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->alias('some.alias.type', testAsset\instantiateAliasTypeWithArguments\ClassA::class)
                    ->arguments()
                        ->intArgument('intArg', 42)
                        ->floatArgument('floatArg', 42.0)
                        ->stringArgument('stringArg', 'Hello World')
                        ->boolArgument('boolArg', true)
                        ->objectArgument('objectArg', stdClass::class)
                        ->intArgument('intArgOptional', 42)
                        ->floatArgument('floatArgOptional', 42.0)
                        ->stringArgument('stringArgOptional', 'Hello World')
                        ->boolArgument('boolArgOptional', true)
                        ->objectArgument('objectArgOptional', stdClass::class)
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        /** @var \vinyl\diTest\integration\testAsset\instantiateAliasTypeWithArguments\ClassA $obj */
        $obj = $di->get('some.alias.type');
        self::assertSame(42, $obj->intArg);
        self::assertSame(42, $obj->intArgOptional);
        self::assertSame(42.0, $obj->floatArg);
        self::assertSame(42.0, $obj->floatArgOptional);
        self::assertSame('Hello World', $obj->stringArg);
        self::assertSame('Hello World', $obj->stringArgOptional);
        self::assertTrue($obj->boolArg);
        self::assertTrue($obj->boolArgOptional);
        self::assertInstanceOf(stdClass::class, $obj->objectArg);
        self::assertInstanceOf(stdClass::class, $obj->objectArgOptional);
    }

    /**
     * @test
     */
    public function virtualTypeArgumentInheritance(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(ClassA::class)
                    ->arguments()
                        ->stringArgument('param1','param1')
                    ->endArguments()
                ->end()
                ->classDefinition(ClassB::class)
                    ->inheritArguments(true)
                    ->arguments()
                        ->stringArgument('param3','param3')
                    ->endArguments()
                ->end()
                ->classDefinition(ClassC::class)
                    ->inheritArguments(true)
                    ->arguments()
                        ->stringArgument('param4','param4')
                    ->endArguments()
                ->end()

                ->alias('virtual.def.1', ClassC::class)
                    ->inheritArguments(true)
                    ->arguments()
                        ->stringArgument('param1','vparam1')
                    ->endArguments()
                ->end()

                ->aliasOnAlias('virtual.def.2', 'virtual.def.1')
                    ->inheritArguments(true)
                    ->arguments()
                        ->stringArgument('param1','virtual.def.2')
                    ->endArguments()
                ->end()
                ->aliasOnAlias('virtual.def.3', 'virtual.def.2')
                    ->inheritArguments(true)
                    ->arguments()
                        ->stringArgument('param4','virtual.def.3')
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        /** @var ClassC $obj */
        $obj = $di->get('virtual.def.3');
        self::assertInstanceOf(ClassC::class, $obj);
        self::assertInstanceOf(stdClass::class, $obj->param2);
        self::assertSame('virtual.def.3', $obj->param4);
        self::assertSame('param3', $obj->param3);
        self::assertSame('virtual.def.2', $obj->param1);
    }

    /**
     * @test
     */
    public function instantiateAlwaysSameAlias(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->alias('always.same', testAsset\instantiateAlwaysSameAlias\ClassA::class)->end();
            // @formatter:on
        });

        $firstCall = $di->get('always.same');
        $secondCall = $di->get('always.same');
        self::assertSame($firstCall, $secondCall);
    }

    /**
     * @test
     */
    public function instantiateAlwaysNewAlias(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\instantiateAlwaysNewAlias\ClassA::class)->end()
                ->alias('always.same', testAsset\instantiateAlwaysNewAlias\ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end();
            // @formatter:on
        });

        $firstCall = $di->get('always.same');
        $secondCall = $di->get('always.same');

        self::assertNotSame($firstCall, $secondCall);
    }

    /**
     * @test
     */
    public function exceptionHappenedOnNotAllArgumentsProvided(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\exceptionHappenedOnNotAllArgumentsProvided\ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end();
            // @formatter:on
        });

        $this->expectException(NotEnoughArgumentsPassedException::class);

        $di->get(testAsset\exceptionHappenedOnNotAllArgumentsProvided\ClassA::class);
    }

    /**
     * @test
     */
    public function autoGeneratedProxyUsedAsConstructorArgument(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
            ->classDefinition(testAsset\autoGeneratedProxyUsedAsConstructorArgument\ClassA::class)
                ->arguments()
                    ->proxyArgument('class', null)
                    ->arrayMapArgument('arrayMap')
                        ->proxyItem('first', stdClass::class)
                        ->proxyItem('second', stdClass::class)
                    ->end()
                    ->arrayListArgument('arrayList')
                        ->proxyItem(stdClass::class)
                    ->end()
                ->endArguments()
            ->end();
            // @formatter:on
        });

        $obj = $di->get(testAsset\autoGeneratedProxyUsedAsConstructorArgument\ClassA::class);

        self::assertInstanceOf(VirtualProxyInterface::class, $obj->class);
        self::assertInstanceOf(VirtualProxyInterface::class, $obj->arrayList[0]);
        self::assertInstanceOf(VirtualProxyInterface::class, $obj->arrayMap['first']);
        self::assertInstanceOf(VirtualProxyInterface::class, $obj->arrayMap['second']);
    }

    /**
     * @test
     */
    public function noConflictInAutoGeneratedProxyName(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\noConflictInProxyNameAndAlias\ClassA::class)
                    ->arguments()
                        ->proxyArgument('b', testAsset\noConflictInProxyNameAndAlias\ClassB::class)
                        ->proxyArgument('c', 'vinyl.ditest.integration.testasset.noconflictinproxynameandalias.classb')
                    ->endArguments()
                ->end()
                ->alias(
                    'vinyl.ditest.integration.testasset.noconflictinproxynameandalias.classb',
                    testAsset\noConflictInProxyNameAndAlias\ClassB::class
                )
                    ->arguments()
                        ->stringArgument('id', 'alias')
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $classA = $di->get(testAsset\noConflictInProxyNameAndAlias\ClassA::class);

        self::assertEquals('default', $classA->b->id());
        self::assertEquals('alias', $classA->c->id());
    }

    /**
     * @test
     */
    public function proxyGenerationForAlias(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\proxyGenerationForAlias\ClassA::class)
                    ->arguments()
                        ->proxyArgument('classB', 'class.b')
                    ->endArguments()
                ->end()
                ->classDefinition(testAsset\proxyGenerationForAlias\ClassB::class)
                    ->arguments()
                        ->stringArgument('value', 'test')
                    ->endArguments()
                ->end()
                ->alias('class.b',testAsset\proxyGenerationForAlias\ClassB::class)
                    ->arguments()
                        ->stringArgument('value', 'hello world')
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $classA = $di->get(testAsset\proxyGenerationForAlias\ClassA::class);
        self::assertEquals('hello world', $classA->classB()->value());
    }

    /**
     * @test
     */
    public function proxyInheritLifetime(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\proxyInheritLifetime\ClassC::class)
                    ->arguments()
                        ->proxyArgument('first', 'class.b.alias')
                        ->proxyArgument('second', 'class.b.alias')
                    ->endArguments()
                ->end()

                ->classDefinition(testAsset\proxyInheritLifetime\ClassA::class)
                    ->arguments()
                        ->proxyArgument('first', null)
                        ->proxyArgument('second', null)
                    ->endArguments()
                ->end()

                ->classDefinition(testAsset\proxyInheritLifetime\ClassB::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end()

                ->alias('class.b.alias', testAsset\proxyInheritLifetime\ClassB::class)->end();
            // @formatter:on
        });

        $object = $di->get(testAsset\proxyInheritLifetime\ClassA::class);
        self::assertNotSame($object->first, $object->second);

        $classC = $di->get(testAsset\proxyInheritLifetime\ClassC::class);
        self::assertNotSame($classC->first, $classC->second);
    }

    /**
     * @test
     */
    public function proxyGenerationForInterface(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            $containerBuilder
                ->classDefinition(testAsset\proxyGenerationForInterface\ClassA::class)
                    ->arguments()
                        ->proxyArgument('interfaceB', null)
                    ->endArguments()
                ->end()
                ->interfaceImplementation(
                    testAsset\proxyGenerationForInterface\InterfaceB::class,
                    testAsset\proxyGenerationForInterface\ClassB::class
                );
        });

        $classA = $di->get(testAsset\proxyGenerationForInterface\ClassA::class);
        self::assertInstanceOf(VirtualProxyInterface::class, $classA->interfaceB);
        self::assertInstanceOf(testAsset\proxyGenerationForInterface\ClassB::class, $classA->interfaceB);
        self::assertEquals('Hello world', $classA->interfaceB->test());
    }

    /**
     * @test
     */
    public function proxyIsGeneratedOnReplacedClass(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $definitionMapBuilder): void {
            $definitionMapBuilder
                ->classDefinition(testAsset\proxyIsGeneratedOnReplacedClass\ClassA::class)
                    ->arguments()
                        ->proxyArgument('classB', null)
                    ->endArguments()
                ->end()
                ->classDefinition(testAsset\proxyIsGeneratedOnReplacedClass\ClassB::class)
                    ->replaceClass(testAsset\proxyIsGeneratedOnReplacedClass\ClassC::class)
                ->end();
        });

        $classA = $di->get(testAsset\proxyIsGeneratedOnReplacedClass\ClassA::class);
        self::assertInstanceOf(VirtualProxyInterface::class, $classA->classB);
        self::assertInstanceOf(testAsset\proxyIsGeneratedOnReplacedClass\ClassC::class, $classA->classB);
    }

    /**
     * @test
     */
    public function aliasTypeCircularDependencyDetection(): void
    {
        $this->expectException(DefinitionTransformerException::class);

        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
        $containerBuilder
            ->aliasOnAlias('first.virtual.type', 'second.virtual.type')->end()
            ->aliasOnAlias('second.virtual.type', 'first.virtual.type')->end();
            // @formatter:on
        });

        $di->get('first.virtual.type');
    }

    /**
     * @test
     */
    public function automaticRegisterClassArgument(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
        $containerBuilder
            ->classDefinition(testAsset\automaticRegisterClassArgument\ClassA::class)
                ->arguments()
                    ->classArgument('className', testAsset\automaticRegisterClassArgument\ClassB::class)
                    ->arrayMapArgument('classMap')
                        ->classItem('class',testAsset\automaticRegisterClassArgument\ClassC::class)
                    ->end()
                    ->arrayListArgument('classList')
                        ->classItem(testAsset\automaticRegisterClassArgument\ClassD::class)
                    ->end()
                ->endArguments()
            ->end();
            // @formatter:on
        });

        self::assertInstanceOf(
            testAsset\automaticRegisterClassArgument\ClassB::class,
            $di->get(testAsset\automaticRegisterClassArgument\ClassB::class)
        );
        self::assertInstanceOf(
            testAsset\automaticRegisterClassArgument\ClassC::class,
            $di->get(testAsset\automaticRegisterClassArgument\ClassC::class)
        );
        self::assertInstanceOf(
            testAsset\automaticRegisterClassArgument\ClassD::class,
            $di->get(testAsset\automaticRegisterClassArgument\ClassD::class)
        );
    }

    /**
     * @test
     */
    public function instantiateObjectWithSortedArrayList(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
            ->classDefinition(testAsset\instantiateObjectWithSortedArrayList\ClassA::class)
                ->arguments()
                    ->arrayListArgument('items')
                        ->intItem(10,40)
                        ->stringItem('b', 30)
                        ->floatItem(10.5, 20)
                        ->boolItem(true, 10)
                        ->objectItem(stdClass::class, 4)
                        ->proxyItem(stdClass::class, 3)
                    ->end()
                ->endArguments()
            ->end()
            ->alias('test.type', testAsset\instantiateObjectWithSortedArrayList\ClassA::class)
                ->inheritArguments(true)
                ->arguments()
                    ->arrayListArgument('items')
                        ->stringItem('e',1)
                        ->stringItem('f',50)
                        ->stringItem('g',5)
                    ->end()
                ->endArguments()
            ->end();
            // @formatter:on
        });

        /** @var testAsset\instantiateObjectWithSortedArrayList\ClassA $alias */
        $obj = $di->get(testAsset\instantiateObjectWithSortedArrayList\ClassA::class);
        $alias = $di->get('test.type');
        $proxy = $obj->items[0];
        self::assertEquals([$proxy, new stdClass, true, 10.5, 'b', 10,], $obj->items);
        self::assertEquals(['e', $proxy, new stdClass, 'g', true, 10.5, 'b', 10, 'f',], $alias->items);
    }

    /**
     * @test
     */
    public function instantiateObjectWithSortedArrayMap(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\instantiateObjectWithSortedArrayMap\ClassA::class)
                    ->arguments()
                        ->arrayMapArgument('items')
                            ->intItem('a',10,40)
                            ->stringItem('b', 'b', 30)
                            ->floatItem('c', 10.5, 20)
                            ->boolItem('d', true, 10)
                            ->objectItem('e',stdClass::class, 4)
                            ->proxyItem('f', stdClass::class, 3)
                        ->end()
                    ->endArguments()
                ->end()
                ->alias('test.type', testAsset\instantiateObjectWithSortedArrayMap\ClassA::class)
                    ->inheritArguments(true)
                    ->arguments()
                        ->arrayMapArgument('items')
                            ->stringItem('g', 'e',1)
                            ->stringItem('h','f',50)
                            ->stringItem('i','g',5)
                        ->end()
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        /** @var testAsset\instantiateObjectWithSortedArrayMap\ClassA $alias */
        $obj = $di->get(testAsset\instantiateObjectWithSortedArrayMap\ClassA::class);
        $alias = $di->get('test.type');
        $proxy = $obj->items['f'];
        self::assertEquals([$proxy, new stdClass, true, 10.5, 'b', 10,], array_values($obj->items));
        self::assertEquals(['e', $proxy, new stdClass, 'g', true, 10.5, 'b', 10, 'f',], array_values($alias->items));
    }

    /**
     * @test
     */
    public function checksServiceAvailabilityInContainer(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\checksServiceAvailabilityInContainer\ClassA::class)->end();
            // @formatter:on
        });

        self::assertTrue($di->has(testAsset\checksServiceAvailabilityInContainer\ClassA::class));
        self::assertFalse($di->has('Undefined'));
    }

    /**
     * @test
     */
    public function aliasUsedAsArgument(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\aliasUsedAsArgument\ClassA::class)
                    ->arguments()
                        ->objectArgument('classB', 'test.test')
                    ->endArguments()
                ->end()
                ->alias('test.test', testAsset\aliasUsedAsArgument\ClassB::class)
                ->end();
            // @formatter:on
        });

        $object = $di->get(testAsset\aliasUsedAsArgument\ClassA::class);

        self::assertInstanceOf(
            testAsset\aliasUsedAsArgument\ClassA::class,
            $object
        );
    }

    /**
     * @test
     */
    public function interfaceImplementationOverriddenViaArgument(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->interfaceImplementation(testAsset\interfaceImplementationOverriddenViaArgument\InterfaceA::class, testAsset\interfaceImplementationOverriddenViaArgument\ClassA::class)
                ->classDefinition(testAsset\interfaceImplementationOverriddenViaArgument\ClassC::class)
                    ->arguments()
                        ->objectArgument('interfaceA', testAsset\interfaceImplementationOverriddenViaArgument\ClassB::class)
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        self::assertInstanceOf(
            testAsset\interfaceImplementationOverriddenViaArgument\ClassB::class,
            $di->get(testAsset\interfaceImplementationOverriddenViaArgument\ClassC::class)->interfaceA
        );
    }

    /**
     * @test
     */
    public function allDependenciesArePrototype(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $containerBuilder): void {
            // @formatter:off
            $containerBuilder
                ->classDefinition(testAsset\allDependenciesArePrototype\ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end()
                ->classDefinition(testAsset\allDependenciesArePrototype\ClassB::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end()
                ->classDefinition(testAsset\allDependenciesArePrototype\ClassC::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end();
            // @formatter:on
        });

        $firstInstance = $di->get(testAsset\allDependenciesArePrototype\ClassA::class);
        $secondInstance = $di->get(testAsset\allDependenciesArePrototype\ClassA::class);

        self::assertNotSame($firstInstance, $secondInstance);
        self::assertNotSame($firstInstance->classB, $secondInstance->classB);
        self::assertNotSame($firstInstance->classB->classC, $secondInstance->classB->classC);
    }

    /**
     * @test
     */
    public function abstractClassUsedAsArgument(): void
    {
        $this->expectException(DefinitionTransformerException::class);
        $di = $this->createContainer(static function(DefinitionMapBuilder $definitionMapBuilder):void {
            $definitionMapBuilder
                ->classDefinition(testAsset\abstractClassUsedAsArgument\ClassA::class)->end();
        });

        $di->get(testAsset\abstractClassUsedAsArgument\ClassA::class);
    }

    /**
     * @test
     */
    public function abstractOptionalClassUsedAsArgument(): void
    {
        $di = $this->createContainer(static function(DefinitionMapBuilder $definitionMapBuilder):void {
            $definitionMapBuilder
                ->classDefinition(testAsset\abstractNullableClassUsedAsArgument\ClassA::class)->end();
        });

        $obj = $di->get(testAsset\abstractNullableClassUsedAsArgument\ClassA::class);
        self::assertNull($obj->classB);
    }

    /**
     * @test
     */
    public function instantiateObjectWithStaticMethodAsInstantiator(): void
    {
        $di = $this->createContainer(static function(DefinitionMapBuilder $definitionMapBuilder):void {
            $testClass = testAsset\instantiateObjectWithStaticConstructor\ClassA::class;
            $definitionMapBuilder
                ->classDefinition($testClass)
                    ->changeInstantiator(StaticMethodInstantiator::create($testClass,'create'))
                    ->arguments()
                        ->stringArgument('data', 'hello world')
                    ->endArguments()
                ->end()
                ->alias('without.arguments', $testClass)
                    ->changeInstantiator(StaticMethodInstantiator::create($testClass,'createWithoutArguments'))
                ->end();
        });

        $obj = $di->get(testAsset\instantiateObjectWithStaticConstructor\ClassA::class);
        self::assertInstanceOf(
            testAsset\instantiateObjectWithStaticConstructor\ClassA::class,
            $obj
        );
        self::assertEquals('hello world', $obj->data);

        $alias = $di->get('without.arguments');
        self::assertInstanceOf(
            testAsset\instantiateObjectWithStaticConstructor\ClassA::class,
            $alias
        );
        self::assertEquals('without arguments', $alias->data);
    }

    /**
     * @test
     */
    public function instantiateObjectWithFunctionAsInstantiator(): void
    {
        $di = $this->createContainer(static function(DefinitionMapBuilder $definitionMapBuilder):void {
            $function = 'vinyl\diTest\integration\testAsset\instantiateObjectWithFunctionAsInstantiator\create_class_a';
            $definitionMapBuilder
                ->classDefinition(testAsset\instantiateObjectWithFunctionAsInstantiator\ClassA::class)
                    ->changeInstantiator(FunctionInstantiator::create($function))
                    ->arguments()
                        ->stringArgument('message', 'Hello World')
                    ->endArguments()
                ->end();
        });

        $obj = $di->get(testAsset\instantiateObjectWithFunctionAsInstantiator\ClassA::class);
        self::assertInstanceOf(testAsset\instantiateObjectWithFunctionAsInstantiator\ClassA::class, $obj);
        self::assertEquals('Hello World', $obj->message);
    }

    /**
     * @test
     */
    public function classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation(): void
    {
        $di = $this->createContainer(static function(DefinitionMapBuilder $definitionMapBuilder):void {
            $definitionMapBuilder
                ->classDefinition(testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassA::class)
                    ->arguments()
                        ->objectArgument('otherObject', testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassC::class)
                        ->proxyArgument('classD', null)
                    ->endArguments()
                ->end()
                ->alias('alias', testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassE::class);
        });

        $exceptionHappens = false;
        try {
            $di->get(testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassB::class);
        } catch (NotFoundException $e) {
            $exceptionHappens = true;
        }

        self::assertTrue($exceptionHappens);
        $exceptionHappens = false;

        try {
            $di->get(testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassC::class);
        } catch (NotFoundException $e) {
            $exceptionHappens = true;
        }

        self::assertTrue($exceptionHappens);
        $exceptionHappens = false;

        try {
            $di->get(testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassD::class);
        } catch (NotFoundException $e) {
            $exceptionHappens = true;
        }

        self::assertTrue($exceptionHappens);
        $exceptionHappens = false;

        try {
            $di->get(testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassE::class);
        } catch (NotFoundException $e) {
            $exceptionHappens = true;
        }

        self::assertTrue($exceptionHappens);
    }

    /**
     * @test
     */
    public function instantiateObjectWithVariadicArgument(): void
    {
        $this->markTestSkipped('implement after introducing variadicArgument() in builder.');
    }

    /**
     * @test
     */
    public function aliasInheritLifetime(): void
    {
        $di = $this->createContainer(static function(DefinitionMapBuilder $definitionMapBuilder):void {
            $definitionMapBuilder
                ->classDefinition(testAsset\aliasInheritLifetime\ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end()
                ->alias('alias', testAsset\aliasInheritLifetime\ClassA::class)->end()
                ->aliasOnAlias('alias.on.alias', 'alias')->end();
        });

        self::assertNotSame($di->get('alias'), $di->get('alias'));
        self::assertNotSame($di->get('alias.on.alias'), $di->get('alias.on.alias'));
    }

    /**
     * @test
     */
    public function aliasInheritInstantiator(): void
    {
        $di = $this->createContainer(static function (DefinitionMapBuilder $dmb) {
            // @formatter:off
            $dmb->classDefinition(testAsset\aliasInheritInstantiator\ClassA::class)
                ->changeInstantiator(
                    StaticMethodInstantiator::create(testAsset\aliasInheritInstantiator\ClassA::class, 'create')
                )
                ->end();

            $dmb->alias('test', testAsset\aliasInheritInstantiator\ClassA::class)->end();
            $dmb->aliasOnAlias('test.2', 'test')->end();
            // @formatter:on
        });

        self::assertInstanceOf(testAsset\aliasInheritInstantiator\ClassA::class, $di->get('test'));
        self::assertInstanceOf(testAsset\aliasInheritInstantiator\ClassA::class, $di->get('test.2'));
    }

    /**
     * Returns di container
     *
     * @param callable $builderFunction (containerBuilder)
     *
     * @return \vinyl\di\Container
     * @throws \vinyl\di\definition\ClassCircularReferenceFoundException
     * @throws \vinyl\di\definition\DefinitionTransformerException
     */
    abstract protected function createContainer(callable $builderFunction): \Psr\Container\ContainerInterface;
}
