<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace vinyl\diBenchmark;

use vinyl\di\definition\DefinitionMap;
use vinyl\di\definition\RecursionFreeDefinitionTransformer;
use vinyl\di\definition\RecursiveDefinitionTransformer;
use vinyl\di\DefinitionMapBuilder;
use vinyl\di\factory\DefinitionMapTransformer;
use vinyl\diBenchmark\metadataBuilder\AssetGenerator;

/**
 * Class MetadataBuilderBench
 *
 * @BeforeMethods({"init"})
 * @Revs(100)
 * @Iterations(8)
 */
class MetadataBuilderBench
{
    private DefinitionMap $definitionContainer;

    public function init()
    {
        $assetGenerator = new AssetGenerator();
        $containerBuilder = new DefinitionMapBuilder();

        $classesToRegister = $assetGenerator->generate();

        foreach ($classesToRegister as $class) {
            $containerBuilder->classDefinition($class)->end();
        }

        $this->definitionContainer = $containerBuilder->build();
    }

    public function benchRecursionFreeDefinitionProcessor()
    {
        (new DefinitionMapTransformer(new RecursionFreeDefinitionTransformer()))->transform($this->definitionContainer);
    }

    public function benchRecursiveDefinitionProcessor()
    {
        (new DefinitionMapTransformer(new RecursiveDefinitionTransformer()))->transform($this->definitionContainer);
    }
}
