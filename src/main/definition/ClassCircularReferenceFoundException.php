<?php

declare(strict_types=1);

namespace vinyl\di\definition;

/**
 * Class ClassCircularReferenceFoundException
 */
final class ClassCircularReferenceFoundException extends DefinitionTransformerException
{
    /**
     * Static constructor of {@see ClassCircularReferenceFoundException}
     *
     * @param array<string, string> $visitedClasses
     */
    public static function create(string $lastVisitedClass, array $visitedClasses): self
    {
        $message = '';

        foreach ($visitedClasses as $visitedClass => $definitionId) {
            $message .= "{$visitedClass}<{$definitionId}> require -> ";
        }

        $lastVisitedDefinitionId = $visitedClasses[$lastVisitedClass];
        $message .= "{$lastVisitedClass}<$lastVisitedDefinitionId>";

        return new self($message);
    }
}
