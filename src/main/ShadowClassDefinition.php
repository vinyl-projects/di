<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\di\definition\UnmodifiableDefinitionMap;
use vinyl\std\lang\ClassObject;
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

    public static function resolveShadowDefinition(string $className, UnmodifiableDefinitionMap $definitionMap): self
    {
        $id = self::makeShadowId($className);

        if ($definitionMap->contains($id)) {
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
