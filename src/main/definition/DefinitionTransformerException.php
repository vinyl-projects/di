<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use Exception;
use stdClass;
use Throwable;

/**
 * Class DefinitionTransformerException
 */
class DefinitionTransformerException extends Exception
{
    /** @var array<string, \stdClass> */
    private array $definitionPath = [];

    /**
     * Static constructor of {@see DefinitionTransformerException}
     */
    public static function createFromException(string $message, Throwable $previousException): self
    {
        return new self($message, $previousException->getCode(), $previousException);
    }

    public function add(string $id, string $class, string $argumentName): void
    {
        $data = new stdClass();
        $data->class = $class;
        $data->argumentName = $argumentName;

        $this->definitionPath[$id] = $data;
    }
}
