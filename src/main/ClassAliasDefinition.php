<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\std\lang\ClassObject;

/**
 * Class ClassAliasDefinition
 */
final class ClassAliasDefinition extends AliasDefinition
{
    /**
     * ClassAliasDefinition constructor.
     */
    public function __construct(string $id, ClassObject $class)
    {
        self::validateAliasId($id);
        parent::__construct($id, $class);
    }
}
