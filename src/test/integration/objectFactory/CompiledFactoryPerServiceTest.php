<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory;

use Closure;
use vinyl\di\ContainerBuilder;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\ObjectFactory;
use function md5;
use function random_bytes;

/**
 * Class CompiledFactoryPerServiceTest
 */
class CompiledFactoryPerServiceTest extends AbstractFactoryTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function createFactory(?Closure $builderFunction = null): ObjectFactory
    {
        $definitionMapBuilder = new DefinitionMapBuilder();
        if ($builderFunction !== null) {
            $builderFunction($definitionMapBuilder);
        }
        $definitionMap = $definitionMapBuilder->build();

        $factoryClassName = self::getNextClassName();
        $container = ContainerBuilder::create($definitionMap)
            ->useEvalMaterializerStrategy()
            ->useCompiledFactory($factoryClassName)
            ->build();

        $factory = $container->get(ObjectFactory::class);

        if (!$factory instanceof $factoryClassName) {
            throw new \RuntimeException('Factory is not instance of CompiledFactory');
        }

        /** @var ObjectFactory $factory */
        return $factory;
    }

    private static function getNextClassName(): string
    {
        return 'vinyl\\diTestAsset\\FactoryClass_' . md5(random_bytes(32));
    }
}
