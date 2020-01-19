<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Class ArgumentTypeNotFoundException
 */
final class ArgumentTypeNotFoundException extends ConstructorMetadataExtractorException
{
    /**
     * Static constructor for {@see ArgumentTypeNotFoundException}
     */
    public static function create(string $type, string $argumentName): self
    {
        return new self("Class [{$type}] not found for argument [{$argumentName}].");
    }
}
