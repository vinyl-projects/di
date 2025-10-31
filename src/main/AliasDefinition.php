<?php

declare(strict_types=1);

namespace vinyl\di;

use LogicException;
use RuntimeException;

/**
 * Class AliasDefinition
 */
final class AliasDefinition extends AbstractDefinition
{
    private string $definitionId;

    public function __construct(string $definitionAliasId, string $definitionId)
    {
        $this->definitionId = $definitionId;

        parent::__construct($definitionAliasId);
    }

    /**
     * {@inheritDoc}
     */
    public function definitionId(): string
    {
        return $this->definitionId;
    }

    /**
     * {@inheritDoc}
     *
     * Will always throw an exception
     */
    public function isArgumentInheritanceEnabled(): bool
    {
        throw new LogicException('This method should not be called on AliasDefinition.');
    }

    /**
     * Unsupported for this definition type. Will always throw {@see RuntimeException} exception
     */
    public function toggleArgumentInheritance(bool $status): ?bool
    {
        throw new LogicException('It is impossible to change inheritance status for alias definition. It is always enabled by default.');
    }
}
