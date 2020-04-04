<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use Exception;
use stdClass;
use Throwable;
use vinyl\di\factory\FactoryMetadata;
use function implode;

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

    public static function createIncompleteException(FactoryMetadata $factoryMetadata): self
    {
        $missedArguments = [];

        foreach ($factoryMetadata->values as $argumentName => $value) {
            if ($value->isMissed()) {
                $missedArguments[] = $argumentName;
            }
        }

        $missedArgumentsString = implode(',', $missedArguments);
        return new self(
            "Definition [{$factoryMetadata->id}] with 'singleton' lifetime could not be incomplete. The next arguments must be set: [{$missedArgumentsString}]"
        );
    }

    public function add(string $id, string $class, string $argumentName): void
    {
        $data = new stdClass();
        $data->class = $class;
        $data->argumentName = $argumentName;

        $this->definitionPath[$id] = $data;
    }
}
