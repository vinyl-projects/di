<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\unit\metadataBuilder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use vinyl\di\ClassDefinition;
use vinyl\di\Definition;
use vinyl\di\definition\DefinitionMap;
use vinyl\di\definition\RecursionFreeValueCollector;
use vinyl\di\definition\value\IntValue;
use vinyl\di\definition\value\StringValue;
use vinyl\di\factory\ParentClassesProvider;
use function get_class;

/**
 * Class RecursionFreeValueCollectorTest
 */
class RecursionFreeValueCollectorTest extends TestCase
{
    /**
     * @test
     */
    public function collectWithDisabledArgumentInheritance(): void
    {
        $testClass = new class
        {
        };

        $definition = new ClassDefinition(get_class($testClass));
        $firstValueHolder = new IntValue(1);
        $secondValueHolder = new StringValue('hello world');
        $definition->argumentValues()->put('first', $firstValueHolder);
        $definition->argumentValues()->put('second', $secondValueHolder);
        $definitions = new DefinitionMap([$definition->id() => $definition]);

        /** @var ParentClassesProvider&MockObject $parentsClassesProvider */
        $parentsClassesProvider = $this->getMockBuilder(ParentClassesProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentsClassesProvider->expects(self::never())->method('find');

        $valueMap = (new RecursionFreeValueCollector($parentsClassesProvider))->collect($definition, $definitions);

        self::assertNotNull($valueMap->find('first'));
        self::assertNotNull($valueMap->find('second'));

        self::assertNotSame($firstValueHolder, $valueMap->find('first'));//TODO remove this tests from here
        self::assertNotSame($secondValueHolder, $valueMap->find('second'));
    }

    /**
     * @test
     */
    public function collectWithEnabledArgumentInheritance(): void
    {
        $first = new class
        {
        };
        $second = new class
        {
        };

        $classesProviderMock = $this->createParentClassesProviderMock([
            [get_class($first), [get_class($second)]],
        ]);

        $mainDefinition = new ClassDefinition(get_class($first));
        $mainDefinition->toggleArgumentInheritance(true);
        $secondDefinition = new ClassDefinition(get_class($second));
        $secondDefinition->toggleArgumentInheritance(true);

        $mainDefinition->argumentValues()->put('first', new IntValue(1));
        $secondDefinition->argumentValues()->put('second', new IntValue(2));

        $definitions = $this->createDefinitionMap($mainDefinition, $secondDefinition);

        $collector = new RecursionFreeValueCollector($classesProviderMock);
        $valueMap = $collector->collect($mainDefinition, $definitions);

        self::assertNotNull($valueMap->find('first'));
        self::assertNotNull($valueMap->find('second'));
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject&ParentClassesProvider
     */
    private function createParentClassesProviderMock(array $willReturnMap): MockObject
    {
        $mock = $this->getMockBuilder(ParentClassesProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('find')->willReturnMap($willReturnMap);

        return $mock;
    }

    private function createDefinitionMap(Definition ... $definitions): DefinitionMap
    {
        $definitionMap = [];

        foreach ($definitions as $definition) {
            $definitionMap[$definition->id()] = $definition;
        }

        return new DefinitionMap($definitionMap);
    }
}
