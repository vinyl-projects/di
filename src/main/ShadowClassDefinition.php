<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;
use function assert;

/**
 * Class ShadowClassDefinition
 */
final class ShadowClassDefinition extends AbstractDefinition
{
    public function __construct(ClassObject $class)
    {
        parent::__construct(self::makeShadowId($class->name()), $class);
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
