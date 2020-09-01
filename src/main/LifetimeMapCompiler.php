<?php

declare(strict_types=1);

namespace vinyl\di;

use LogicException;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;
use function class_exists;
use function implode;

/**
 * Class LifetimeMapCompiler
 */
final class LifetimeMapCompiler
{
    private ClassMaterializer $classMaterializer;

    /**
     * LifetimeMapCompiler constructor.
     */
    public function __construct(ClassMaterializer $classMaterializer)
    {
        $this->classMaterializer = $classMaterializer;
    }

    /**
     * Compiles implementation of {@see \vinyl\di\LifetimeCodeMap}
     * @psalm-param Map<string, \vinyl\di\factory\FactoryMetadata> $factoryMetadataMap
     */
    public function compile(string $className, Map $factoryMetadataMap): ClassObject
    {
        if (class_exists($className)) {
            throw new LogicException("Class {$className} already exists.");
        }

        $map = [];
        /** @var \vinyl\di\factory\FactoryMetadata $factoryMetadata */
        foreach ($factoryMetadataMap as $factoryMetadata) {
            $map[] = "'{$factoryMetadata->id}' => '{$factoryMetadata->lifetimeCode}'";
        }

        $template = self::generateMapTemplate($className, implode(',', $map));
        $this->classMaterializer->materialize($className, $template);

        return ClassObject::create($className);
    }

    private static function generateMapTemplate(string $className, string $mapContent): string
    {
        [$class, $classNamespace] = class_extract_short_name_and_namespace($className);
        $namespace = "namespace {$classNamespace};";

        if (!$classNamespace) {
            $namespace = '';
        }

        return <<<MAP
        declare(strict_types=1);
        
        {$namespace}
        
        use ArrayIterator;
        use OutOfBoundsException;
        use function array_key_exists;
        use function count;
        
        /**
         * Class LifetimeCodeMap
         */
        final class {$class} implements \\vinyl\di\LifetimeCodeMap
        {
            /** @var array<string, string> */
            private array \$map = [
                {$mapContent}
            ];
        
            public function __construct()
            {
            }
        
            /**
             * @return \Iterator<string, string>
             */
            public function getIterator()
            {
                return new ArrayIterator(\$this->map);
            }
        
            /**
             * {@inheritDoc}
             */
            public function count(): int
            {
                return count(\$this->map);
            }
        
            /**
             * {@inheritDoc}
             */
            public function get(string \$definitionId): string
            {
                if (array_key_exists(\$definitionId, \$this->map)) {
                    return \$this->map[\$definitionId];
                }
        
                throw new OutOfBoundsException("Lifetime code for given [{\$definitionId}] id not found.");
            }
        
            /**
             * {@inheritDoc}
             */
            public function contains(string \$definitionId): bool
            {
                return array_key_exists(\$definitionId, \$this->map);
            }
        }
        MAP;
    }
}
