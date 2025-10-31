<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration;

use Composer\InstalledVersions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ProxyManager\Proxy\VirtualProxyInterface;
use Psr\Container\ContainerInterface;
use stdClass;
use vinyl\di\Container;
use vinyl\di\definition\ClassCircularReferenceFoundException;
use vinyl\di\definition\DefinitionTransformerException;
use vinyl\di\definition\FunctionInstantiator;
use vinyl\di\definition\PrototypeLifetime;
use vinyl\di\definition\StaticMethodInstantiator;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\NotEnoughArgumentsPassedException;
use vinyl\di\NotFoundException;
use vinyl\diTest\integration\testAsset\{
    aliasDefinition\argumentInheritance\ClassA,
    aliasDefinition\argumentInheritance\ClassB,
    aliasDefinition\argumentInheritance\ClassC,
    aliasForRuntimeClassDefinition\InterfaceA,
    circularDependencyDetectionWithInterface\ClassAInterface,
    instantiateObjectWithRequiredEnumArgument\EnumArgument,
    instantiateObjectWithRuntimeDefinition\Root,
    instantiateObjectWithRuntimeDefinition\RootChildRuntime,
    instantiateObjectWithRuntimeDefinition\RootRuntimeInterface,
    instantiateObjectWithRuntimeDefinition\RootRuntimeInterfaceImpl
};

/**
 * Class AbstractContainerTest
 */
abstract class AbstractContainerTestCase extends TestCase
{
    private static bool $isProxyManagerInstalled = false;

    private static function skipIfProxyManagerNotInstalled(): void
    {
        if (!self::$isProxyManagerInstalled) {
            self::markTestSkipped('Proxy Manager not installed.');
        }
    }

    protected function setUp(): void
    {
        self::$isProxyManagerInstalled = InstalledVersions::isInstalled('ocramius/proxy-manager');
    }

    #[Test]
    public function instantiateObjectWithoutArguments(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            $dmb->classDefinition(testAsset\instantiateObjectWithoutArguments\ClassA::class)->end();
        });

        self::assertInstanceOf(testAsset\instantiateObjectWithoutArguments\ClassA::class, $di->get(testAsset\instantiateObjectWithoutArguments\ClassA::class));
    }

    #[Test]
    public function instantiateObjectWithArguments(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\instantiateObjectWithArguments\ClassA::class)
                    ->arguments()
                        ->intArgument('a', 42)
                        ->intArgument('b', null)
                        ->floatArgument('c', 42.5)
                        ->floatArgument('d', null)
                        ->intArgument('ddd', 42)
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
        self::assertEquals(42.0, $obj->ddd);
        self::assertTrue($obj->ee);
        self::assertNull($obj->ff);
        self::assertEquals('hello world', $obj->gg);
        self::assertNull($obj->hh);
        self::assertNull($obj->jj);
        self::assertSame([1,2,3], $obj->kk);
        self::assertNull($obj->ll);
        self::assertNull($obj->pp);
    }

    #[Test]
    public function alwaysReturnSameObject(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\alwaysReturnSameObject\ClassA::class)->end();
            // @formatter:on
        });

        $firstCall = $di->get(testAsset\alwaysReturnSameObject\ClassA::class);
        $secondCall = $di->get(testAsset\alwaysReturnSameObject\ClassA::class);

        self::assertSame($firstCall, $secondCall);
    }

    #[Test]
    public function alwaysReturnNewObject(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\alwaysReturnNewObject\ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end();
            // @formatter:on
        });

        $firstCall = $di->get(testAsset\alwaysReturnNewObject\ClassA::class);
        $secondCall = $di->get(testAsset\alwaysReturnNewObject\ClassA::class);

        self::assertNotSame($firstCall, $secondCall);
    }

    #[Test]
    public function instantiateInterfaceImplementation(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\instantiateInterfaceImplementation\ClassA::class)->end()
                ->alias(
                    testAsset\instantiateInterfaceImplementation\InterfaceA::class,
                    testAsset\instantiateInterfaceImplementation\ClassA::class
                );
            // @formatter:on
        });

        $interface = $di->get(testAsset\instantiateInterfaceImplementation\InterfaceA::class);
        $obj = $di->get(testAsset\instantiateInterfaceImplementation\ClassA::class);

        self::assertInstanceOf(testAsset\instantiateInterfaceImplementation\InterfaceA::class, $interface);
        self::assertInstanceOf(testAsset\instantiateInterfaceImplementation\ClassA::class, $interface);
        self::assertSame($interface, $obj);
    }

    #[Test]
    public function instantiateOverwrittenInterfaceImplementation(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->alias(
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

    #[Test]
    public function replaceClassNotOverrideArguments(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->alias(abstractContainerTest\preferenceNotOverrideArguments\A::class, abstractContainerTest\preferenceNotOverrideArguments\B::class)
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

    #[Test]
    public function interfaceUsedAsArgument(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->alias(testAsset\interfaceUsedAsArgument\InterfaceA::class, testAsset\interfaceUsedAsArgument\ClassA::class)
                ->classDefinition(testAsset\interfaceUsedAsArgument\ClassB::class)->end()
                ->classDefinition(testAsset\interfaceUsedAsArgument\ClassA::class)->end();
            // @formatter:on
        });

        $obj = $di->get(testAsset\interfaceUsedAsArgument\ClassB::class);

        self::assertInstanceOf(testAsset\interfaceUsedAsArgument\ClassA::class, $obj->interfaceA);
    }

    #[Test]
    public function optionalInterfaceUsedAsArgument(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\optionalInterfaceUsedAsArgument\ClassA::class)->end();
            // @formatter:on
        });

        $obj = $di->get(testAsset\optionalInterfaceUsedAsArgument\ClassA::class);

        self::assertNull($obj->interfaceB);
    }

    #[Test]
    public function circularDependencyDetection(): void
    {
        $this->expectException(ClassCircularReferenceFoundException::class);

        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(abstractContainerTest\circularDependencyDetection\ClassA::class)->end();
            // @formatter:on
        });

        $di->get(abstractContainerTest\circularDependencyDetection\ClassA::class);
    }

    #[Test]
    public function circularDependencyDetectionWithInterface(): void
    {
        $this->expectException(ClassCircularReferenceFoundException::class);
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->alias(
                    ClassAInterface::class,
                    testAsset\circularDependencyDetectionWithInterface\ClassA::class
                )
                ->classDefinition(testAsset\circularDependencyDetectionWithInterface\ClassA::class)->end();
            // @formatter:on
        });

        $di->get(testAsset\circularDependencyDetectionWithInterface\ClassA::class);
    }

    #[Test]
    public function circularDependencyResolvedByProxyUsage(): void
    {
        self::skipIfProxyManagerNotInstalled();
        $di = static::createContainer(
            static function (DefinitionMapBuilder $dmb): void {
                // @formatter:off
                $dmb->classDefinition(abstractContainerTest\circularDependencyDetection\ClassA::class)->end()

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

    #[Test]
    public function proxiedObjectInjectsItself(): void
    {
        self::skipIfProxyManagerNotInstalled();
        $di = static::createContainer(
            static function (DefinitionMapBuilder $dmb): void {
                // @formatter:off
                $dmb->classDefinition(testAsset\proxiedObjectInjectsItself\ClassA::class)
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

    #[Test]
    public function instantiateObjectWithInheritedArguments(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\instantiateObjectWithInheritedArguments\ClassD::class)
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

    #[Test]
    public function instantiateAliasAsClassArgument(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->virtualType('1some.virtual.type', testAsset\instantiateAliasAsClassArgument\ClassA::class)
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

    #[Test]
    public function instantiateAliasTypeWithArguments(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->virtualType('some.alias.type', testAsset\instantiateAliasTypeWithArguments\ClassA::class)
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

        /** @var testAsset\instantiateAliasTypeWithArguments\ClassA $obj */
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

    #[Test]
    public function virtualTypeArgumentInheritance(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(ClassA::class)
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

                ->virtualType('virtual.def.1', ClassC::class)
                    ->inheritArguments(true)
                    ->arguments()
                        ->stringArgument('param1','vparam1')
                    ->endArguments()
                ->end()

                ->virtualType('virtual.def.2', 'virtual.def.1')
                    ->inheritArguments(true)
                    ->arguments()
                        ->stringArgument('param1','virtual.def.2')
                    ->endArguments()
                ->end()
                ->virtualType('virtual.def.3', 'virtual.def.2')
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

    #[Test]
    public function instantiateAlwaysSameAlias(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->virtualType('always.same', testAsset\instantiateAlwaysSameAlias\ClassA::class)->end();
            // @formatter:on
        });

        $firstCall = $di->get('always.same');
        $secondCall = $di->get('always.same');
        self::assertSame($firstCall, $secondCall);
    }

    #[Test]
    public function instantiateAlwaysNewAlias(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\instantiateAlwaysNewAlias\ClassA::class)->end()
                ->virtualType('always.same', testAsset\instantiateAlwaysNewAlias\ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end();
            // @formatter:on
        });

        $firstCall = $di->get('always.same');
        $secondCall = $di->get('always.same');

        self::assertNotSame($firstCall, $secondCall);
    }

    #[Test]
    public function exceptionHappenedOnNotAllArgumentsProvided(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\exceptionHappenedOnNotAllArgumentsProvided\ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end();
            // @formatter:on
        });

        $this->expectException(NotEnoughArgumentsPassedException::class);

        $di->get(testAsset\exceptionHappenedOnNotAllArgumentsProvided\ClassA::class);
    }

    #[Test]
    public function autoGeneratedProxyUsedAsConstructorArgument(): void
    {
        self::skipIfProxyManagerNotInstalled();
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\autoGeneratedProxyUsedAsConstructorArgument\ClassA::class)
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

    #[Test]
    public function noConflictInAutoGeneratedProxyName(): void
    {
        self::skipIfProxyManagerNotInstalled();
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\noConflictInProxyNameAndAlias\ClassA::class)
                    ->arguments()
                        ->proxyArgument('b', testAsset\noConflictInProxyNameAndAlias\ClassB::class)
                        ->proxyArgument('c', 'vinyl.ditest.integration.testasset.noconflictinproxynameandalias.classb')
                    ->endArguments()
                ->end()
                ->virtualType(
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

    #[Test]
    public function proxyGenerationForVirtualType(): void
    {
        self::skipIfProxyManagerNotInstalled();
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\proxyGenerationForAlias\ClassA::class)
                    ->arguments()
                        ->proxyArgument('classB', 'class.b')
                    ->endArguments()
                ->end()
                ->classDefinition(testAsset\proxyGenerationForAlias\ClassB::class)
                    ->arguments()
                        ->stringArgument('value', 'test')
                    ->endArguments()
                ->end()
                ->virtualType('class.b',testAsset\proxyGenerationForAlias\ClassB::class)
                    ->arguments()
                        ->stringArgument('value', 'hello world')
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $classA = $di->get(testAsset\proxyGenerationForAlias\ClassA::class);
        self::assertEquals('hello world', $classA->classB()->value());
    }

    #[Test]
    public function proxyInheritLifetime(): void
    {
        self::skipIfProxyManagerNotInstalled();
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\proxyInheritLifetime\ClassC::class)
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

                ->virtualType('class.b.alias', testAsset\proxyInheritLifetime\ClassB::class)->end();
            // @formatter:on
        });

        $object = $di->get(testAsset\proxyInheritLifetime\ClassA::class);
        self::assertNotSame($object->first, $object->second);

        $classC = $di->get(testAsset\proxyInheritLifetime\ClassC::class);
        self::assertNotSame($classC->first, $classC->second);
    }

    #[Test]
    public function proxyGenerationForAlias(): void
    {
        self::skipIfProxyManagerNotInstalled();
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            $dmb->classDefinition(testAsset\proxyGenerationForInterface\ClassA::class)
                    ->arguments()
                        ->proxyArgument('interfaceB', null)
                    ->endArguments()
                ->end()
                ->classDefinition(testAsset\proxyGenerationForInterface\ClassB::class)->end()
                ->alias(
                    testAsset\proxyGenerationForInterface\InterfaceB::class,
                    testAsset\proxyGenerationForInterface\ClassB::class
                );
        });

        $classA = $di->get(testAsset\proxyGenerationForInterface\ClassA::class);
        self::assertInstanceOf(VirtualProxyInterface::class, $classA->interfaceB);
        self::assertInstanceOf(testAsset\proxyGenerationForInterface\ClassB::class, $classA->interfaceB);
        self::assertEquals('Hello world', $classA->interfaceB->test());
    }

    #[Test]
    public function proxyIsGeneratedOnReplacedClass(): void
    {
        self::skipIfProxyManagerNotInstalled();
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            $dmb->classDefinition(testAsset\proxyIsGeneratedOnReplacedClass\ClassA::class)
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

    #[Test]
    public function aliasTypeCircularDependencyDetection(): void
    {
        $this->expectException(DefinitionTransformerException::class);

        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
        $dmb->virtualType('first.virtual.type', 'second.virtual.type')->end()
            ->virtualType('second.virtual.type', 'first.virtual.type')->end();
            // @formatter:on
        });

        $di->get('first.virtual.type');
    }

    #[Test]
    public function automaticRegisterClassArgument(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
        $dmb->classDefinition(testAsset\automaticRegisterClassArgument\ClassA::class)
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

    #[Test]
    public function checksServiceAvailabilityInContainer(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\checksServiceAvailabilityInContainer\ClassA::class)->end();
            // @formatter:on
        });

        self::assertTrue($di->has(testAsset\checksServiceAvailabilityInContainer\ClassA::class));
        self::assertFalse($di->has('Undefined'));
    }

    #[Test]
    public function aliasUsedAsArgument(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\aliasUsedAsArgument\ClassA::class)
                    ->arguments()
                        ->objectArgument('classB', 'test.test')
                    ->endArguments()
                ->end()
                ->virtualType('test.test', testAsset\aliasUsedAsArgument\ClassB::class)
                ->end();
            // @formatter:on
        });

        $object = $di->get(testAsset\aliasUsedAsArgument\ClassA::class);

        self::assertInstanceOf(
            testAsset\aliasUsedAsArgument\ClassA::class,
            $object
        );
    }

    #[Test]
    public function interfaceImplementationOverriddenViaArgument(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\interfaceImplementationOverriddenViaArgument\ClassA::class)->end()
                ->alias(testAsset\interfaceImplementationOverriddenViaArgument\InterfaceA::class, testAsset\interfaceImplementationOverriddenViaArgument\ClassA::class)
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

    #[Test]
    public function allDependenciesArePrototype(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\allDependenciesArePrototype\ClassA::class)
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

    #[Test]
    public function abstractClassUsedAsArgument(): void
    {
        $this->expectException(DefinitionTransformerException::class);
        $di = static::createContainer(static function(DefinitionMapBuilder $dmb):void {
            $dmb->classDefinition(testAsset\abstractClassUsedAsArgument\ClassA::class)->end();
        });

        $di->get(testAsset\abstractClassUsedAsArgument\ClassA::class);
    }

    #[Test]
    public function abstractOptionalClassUsedAsArgument(): void
    {
        $di = static::createContainer(static function(DefinitionMapBuilder $dmb):void {
            $dmb->classDefinition(testAsset\abstractNullableClassUsedAsArgument\ClassA::class)->end();
        });

        $obj = $di->get(testAsset\abstractNullableClassUsedAsArgument\ClassA::class);
        self::assertNull($obj->classB);
    }

    #[Test]
    public function instantiateObjectWithStaticMethodAsInstantiator(): void
    {
        $di = static::createContainer(static function(DefinitionMapBuilder $dmb):void {
            $testClass = testAsset\instantiateObjectWithStaticConstructor\ClassA::class;
            $dmb->classDefinition($testClass)
                    ->changeInstantiator(StaticMethodInstantiator::create($testClass,'create'))
                    ->arguments()
                        ->stringArgument('data', 'hello world')
                    ->endArguments()
                ->end()
                ->virtualType('without.arguments', $testClass)
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

    #[Test]
    public function instantiateObjectWithFunctionAsInstantiator(): void
    {
        $di = static::createContainer(static function(DefinitionMapBuilder $dmb):void {
            $function = 'vinyl\diTest\integration\testAsset\instantiateObjectWithFunctionAsInstantiator\create_class_a';
            $dmb
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

    #[Test]
    public function classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation(): void
    {
        $di = static::createContainer(static function(DefinitionMapBuilder $dmb): void {
            $dmb
                ->classDefinition(testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassA::class)
                    ->arguments()
                        ->objectArgument('otherObject', testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassC::class)
                        ->proxyArgument('classD', null)
                    ->endArguments()
                ->end()
                ->virtualType('alias', testAsset\classesDiscoveredInRuntimeMustThrowInCaseDirectInstantiation\ClassE::class);
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

    #[Test]
    public function instantiateObjectWithVariadicArgument(): void
    {
        $this->markTestSkipped('implement after introducing variadicArgument() in builder.');
    }

    #[Test]
    public function aliasInheritLifetime(): void
    {
        $di = static::createContainer(static function(DefinitionMapBuilder $dmb): void {
            $dmb
                ->classDefinition(testAsset\aliasInheritLifetime\ClassA::class)
                    ->lifetime(PrototypeLifetime::get())
                ->end()
                ->virtualType('alias', testAsset\aliasInheritLifetime\ClassA::class)->end()
                ->virtualType('alias.on.alias', 'alias')->end();
        });

        self::assertNotSame($di->get('alias'), $di->get('alias'));
        self::assertNotSame($di->get('alias.on.alias'), $di->get('alias.on.alias'));
    }

    #[Test]
    public function aliasInheritInstantiator(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\aliasInheritInstantiator\ClassA::class)
                ->changeInstantiator(
                    StaticMethodInstantiator::create(testAsset\aliasInheritInstantiator\ClassA::class, 'create')
                )
                ->end();

            $dmb->virtualType('test', testAsset\aliasInheritInstantiator\ClassA::class)->end();
            $dmb->virtualType('test.2', 'test')->end();
            // @formatter:on
        });

        self::assertInstanceOf(testAsset\aliasInheritInstantiator\ClassA::class, $di->get('test'));
        self::assertInstanceOf(testAsset\aliasInheritInstantiator\ClassA::class, $di->get('test.2'));
    }

    #[Test]
    public function instantiateObjectWithOptionalEnumArgument(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\instantiateObjectWithOptionalEnumArgument\ClassA::class)
                ->end();
            // @formatter:on
        });

        $object = $di->get(testAsset\instantiateObjectWithOptionalEnumArgument\ClassA::class);
        self::assertEquals(testAsset\instantiateObjectWithOptionalEnumArgument\EnumTestProvider::ONE, $object->enumTestProvider);
    }

    #[Test]
    public function instantiateObjectWithRequiredEnumArgument(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\instantiateObjectWithRequiredEnumArgument\ClassA::class)
                    ->arguments()
                        ->enumArgument('argument', EnumArgument::ONE)
                        ->stringArgument('argument2', 'not enum')
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $object = $di->get(testAsset\instantiateObjectWithRequiredEnumArgument\ClassA::class);
        self::assertEquals(EnumArgument::ONE, $object->argument);
        self::assertEquals('not enum', $object->argument2);
    }

    #[Test]
    public function instantiateObjectWithNullableEnumArguments(): void
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\instantiateObjectWithNullableEnumArgument\ClassA::class)
                    ->arguments()
                        ->enumArgument('argument', null)
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $object = $di->get(testAsset\instantiateObjectWithNullableEnumArgument\ClassA::class);
        self::assertNull($object->argument);
        self::assertNull($object->argument2);
    }

    #[Test]
    public function instantiateObjectWithUnionArguments()
    {
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\instantiateObjectWithUnionArguments\ClassA::class)
                    ->arguments()
                        ->intArgument('argument', null)
                    ->endArguments()
                ->end();
            $dmb->virtualType('union.string.param', testAsset\instantiateObjectWithUnionArguments\ClassA::class)
                    ->arguments()
                        ->stringArgument('argument', 'Hello World')
                    ->endArguments();
            $dmb->virtualType('union.int.param', testAsset\instantiateObjectWithUnionArguments\ClassA::class)
                    ->arguments()
                        ->intArgument('argument', 42)
                    ->endArguments();
            $dmb->virtualType('union.enum.param', testAsset\instantiateObjectWithUnionArguments\ClassA::class)
                    ->arguments()
                        ->enumArgument('argument', testAsset\instantiateObjectWithUnionArguments\EnumArgument::DEBUG)
                    ->endArguments();
            // @formatter:on
        });

        self::assertNull($di->get(testAsset\instantiateObjectWithUnionArguments\ClassA::class)->argument);
        self::assertEquals('Hello World', $di->get('union.string.param')->argument);
        self::assertEquals(42, $di->get('union.int.param')->argument);
        self::assertEquals(testAsset\instantiateObjectWithUnionArguments\EnumArgument::DEBUG, $di->get('union.enum.param')->argument);
    }

    #[Test]
    public function instantiateObjectWithIntersectionArguments(): void
    {

        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            // @formatter:off
            $dmb->classDefinition(testAsset\instantiateObjectWithIntersectionArguments\ClassB::class)
                    ->arguments()
                        ->objectArgument('param1', testAsset\instantiateObjectWithIntersectionArguments\ClassA::class)
                        ->objectArgument('param2', null)
                        ->objectArgument('param3', testAsset\instantiateObjectWithIntersectionArguments\ClassA::class)
                        ->arrayListArgument('param5')
                            ->intItem(1)
                        ->end()
                    ->endArguments()
                ->end();
            // @formatter:on
        });

        $object = $di->get(testAsset\instantiateObjectWithIntersectionArguments\ClassB::class);
        self::assertInstanceOf(testAsset\instantiateObjectWithIntersectionArguments\ClassB::class, $object);
        self::assertInstanceOf(testAsset\instantiateObjectWithIntersectionArguments\ClassA::class, $object->param1);
        self::assertInstanceOf(testAsset\instantiateObjectWithIntersectionArguments\ClassA::class, $object->param3);
        self::assertNull($object->param2);
        self::assertNull($object->param4);
        self::assertSame([1], $object->param5);
    }

    #[Test]
    public function checkIfInstanceLifetimeRegisteredInContainer(): void
    {
        $container = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            $dmb->classDefinition(\stdClass::class)->end();
        });

        self::assertTrue($container->lifetimeMap()->contains(\stdClass::class));
    }

    #[Test]
    public function instantiateObjectWithRuntimeDefinition(): void
    {
        // @formatter:off
        $di = static::createContainer(static function (DefinitionMapBuilder $dmb): void {
            $dmb->classDefinition(Root::class)->end();
            $dmb->runtimeClassDefinition(RootChildRuntime::class);
            $dmb->runtimeInterfaceDefinition(RootRuntimeInterface::class);
        });

        $object = new RootChildRuntime();
        $impl = new RootRuntimeInterfaceImpl();
        $di->insertSharedInstance(RootChildRuntime::class, $object);
        $di->insertSharedInstance(RootRuntimeInterface::class, $impl);

        $root = $di->get(Root::class);
        self::assertSame($object, $root->childRuntime);
        self::assertSame($impl, $root->rootRuntime);
    }

    #[Test]
    public function aliasOnVirtualType():void
    {
        // @formatter:off
        $di = static::createContainer(
            static function (DefinitionMapBuilder $dmb): void {
                $dmb->classDefinition(testAsset\aliasOnVirtualType\ClassA::class)
                        ->arguments()
                            ->stringArgument('data', 'class')
                        ->endArguments()
                    ->end();
                $dmb->virtualType('class.a', testAsset\aliasOnVirtualType\ClassA::class)
                        ->arguments()
                            ->stringArgument('data', 'virtual type')
                        ->endArguments()
                    ->end();
                $dmb->alias('class.a.alias', 'class.a');
            }
        );

        $virtualType = $di->get('class.a');
        $alias = $di->get('class.a.alias');
        self::assertSame($virtualType, $alias);
        self::assertSame('virtual type', $virtualType->data);
    }

    #[Test]
    public function aliasForRuntimeClassDefinition(): void
    {
        // @formatter:off
        $di = static::createContainer(
            static function (DefinitionMapBuilder $dmb): void {
                $dmb->runtimeClassDefinition(testAsset\aliasForRuntimeClassDefinition\ClassA::class);
                $dmb->alias(
                    InterfaceA::class,
                    testAsset\aliasForRuntimeClassDefinition\ClassA::class
                );
            }
        );

        $di->insertSharedInstance(InterfaceA::class, new testAsset\aliasForRuntimeClassDefinition\ClassA());
        self::assertInstanceOf(
            testAsset\aliasForRuntimeClassDefinition\ClassA::class,
            $di->get(InterfaceA::class)
        );
    }

    /**
     * Returns di container
     *
     * @param callable(DefinitionMapBuilder):void $builderFunction
     *
     * @return Container
     * @throws ClassCircularReferenceFoundException
     * @throws DefinitionTransformerException
     */
    abstract protected static function createContainer(callable $builderFunction): ContainerInterface;
}
