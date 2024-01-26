<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;
use function assert;

/**
 * Class ShadowClassDefinition
 */
final class ShadowClassDefinition extends AbstractDefinition implements RootDefinition
{
    private ClassObject $classObject;

    public function __construct(ClassObject $class)
    {
        $this->classObject = $class;
        parent::__construct(self::makeShadowId($class->name()));
    }

    /**
     * {@inheritDoc}
     */
    public function classObject(): ClassObject
    {
        return $this->classObject;
    }

    /**
     * {@inheritDoc}
     */
    public function replaceClass(ClassObject $newCLass): ClassObject
    {
        throw new \LogicException("Class could not be changed for Shadow definition. [{$this->id()}]");
    }

    /**
     * @param Map<string, \vinyl\di\Definition> $definitionMap
     */
    public static function resolveShadowDefinition(string $className, Map $definitionMap): self
    {
        $id = self::makeShadowId($className);

        if ($definitionMap->containsKey($id)) {
            $definition = $definitionMap->get($id);
            assert($definition instanceof self);

            return $definition;
        }

        return new self(ClassObject::create($className));
    }

    private static function makeShadowId(string $className): string
    {
        return "{$className}_shadow_definition";
    }
}
