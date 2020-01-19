<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Class DefinitionCircularReferenceFoundException
 */
final class DefinitionCircularReferenceFoundException extends ClassResolverException
{
    /**
     * Static constructor of {@see DefinitionCircularReferenceFoundException}
     *
     * @param array<string, bool> $visitedDefinitions
     */
    public static function create(string $lastVisitedDefinitionId, array $visitedDefinitions): self
    {
        $items = array_keys($visitedDefinitions);
        $items[] = $lastVisitedDefinitionId;
        $message = implode(' require -> ', $items);

        return new self($message);
    }
}
