<?php

declare(strict_types=1);

namespace vinyl\di;

use LogicException;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;
use function array_fill_keys;
use function array_merge;
use function class_exists;
use function count;
use function var_export;

final readonly class IdResolverCompiler
{
    public function __construct(private ClassMaterializer $classMaterializer)
    {
    }

    /**
     * Compiles a new IdResolver based on the provided class name and metadata map.
     *
     * @param string                                         $className
     * @param Map<string, \vinyl\di\factory\FactoryMetadata> $factoryMetadataMap
     *
     * @return \vinyl\std\lang\ClassObject
     */
    public function compile(string $className, Map $factoryMetadataMap): ClassObject
    {
        if (class_exists($className)) {
            throw new LogicException("Class {$className} already exists.");
        }

        /** @var list<array<string, string>> $aliasMap */
        $aliasMap = [[]];
        foreach ($factoryMetadataMap as $item) {
            if (count($item->aliases) <= 0) {
                continue;
            }

            $aliasMap[] = array_fill_keys($item->aliases, $item->id);
        }

        $exportedAliases = var_export(array_merge(...$aliasMap), true);
        $classContent = self::generateTemplate($className, $exportedAliases);
        $this->classMaterializer->materialize($className, $classContent);

        return ClassObject::create($className);
    }

    private static function generateTemplate(string $className, string $exportedAliases): string
    {
        [$class, $classNamespace] = classExtractShortNameAndNamespace($className);
        $namespace = "namespace {$classNamespace};";

        if (!$classNamespace) {
            $namespace = '';
        }

        return <<<MAP
        declare(strict_types=1);
        
        {$namespace}
        
        final class {$class} implements \\vinyl\di\IdResolver
        {
            private array \$aliasMap = {$exportedAliases};
        
            public function resolve(string \$id): string
            {
                return \$this->aliasMap[\$id] ?? \$id;
            }
        }
        MAP;
    }
}
