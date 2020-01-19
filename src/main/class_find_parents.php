<?php

declare(strict_types=1);

namespace vinyl\di;

use InvalidArgumentException;
use SplStack;
use function class_exists;
use function get_parent_class;

/**
 * Returns parent classes
 *
 * @return string[] An array list of parent classes
 * @throws InvalidArgumentException is thrown if the given class not exists
 */
function class_find_parents(string $class): array
{
    if (!class_exists($class)) {
        throw new InvalidArgumentException("Class [{$class}] not exists.");
    }

    $parentClass = get_parent_class($class);

    if ($parentClass === false) {
        return [];
    }

    /** @var SplStack<string> $stack */
    $stack = new SplStack();
    $stack->push($parentClass);
    $result = [];

    while (!$stack->isEmpty()) {
        $currentClass = $stack->pop();
        $result[] = $currentClass;

        $parentClass = get_parent_class($currentClass);

        if ($parentClass === false) {
            continue;
        }

        $stack->push($parentClass);
    }

    return $result;
}
