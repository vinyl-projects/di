<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration;

use Psr\Container\ContainerInterface;
use vinyl\di\ContainerBuilder;
use vinyl\di\DefinitionMapBuilder;
use function md5;
use function random_bytes;

/**
 * Class ServicePerFactoryContainerTest
 */
class ServicePerFactoryContainerTest extends AbstractContainerTest
{
    protected function createContainer(callable $builderFunction): ContainerInterface
    {
        $definitionMapBuilder = new DefinitionMapBuilder();
        $builderFunction($definitionMapBuilder);
        $definitionMap = $definitionMapBuilder->build();

        return ContainerBuilder::create($definitionMap)
            ->useEvalMaterializerStrategy()
            ->useCompiledFactory(self::getNextClassName(), '')
            ->build();
    }

    private static function getNextClassName(): string
    {
        return 'Vinyl\\FactoryClass_' . md5(random_bytes(32));
    }
}
