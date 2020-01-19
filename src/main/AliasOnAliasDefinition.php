<?php

declare(strict_types=1);

namespace vinyl\di;

use RuntimeException;

/**
 * Class AliasOnAliasDefinition
 */
final class AliasOnAliasDefinition extends AbstractDefinition
{
    private string $parentId;

    /**
     * AliasDefinition constructor.
     */
    public function __construct(string $id, string $parentId)
    {
        AliasDefinition::validateAliasId($id);
        AliasDefinition::validateAliasId($parentId);
        parent::__construct($id, null);
        $this->parentId = $parentId;
        $this->toggleArgumentInheritance(true);
    }

    /**
     * Returns an id of parent definition
     */
    public function parentId(): string
    {
        return $this->parentId;
    }

    /**
     * Unsupported operation. Always throws {@see RuntimeException}
     */
    public function replaceClass(string $newCLass): string
    {
        //TODO maybe we should allow changing class for type with parent id
        //TODO so that it could be possible to change class only for particular alias
        throw new RuntimeException('It is impossible to replace class for definition with parent id.');
    }
}
