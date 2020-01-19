<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

use InvalidArgumentException;

/**
 * Class ObjectValue
 */
final class ObjectValue implements \vinyl\di\definition\ObjectValue
{
    private ?string $value;

    /**
     * ObjectValueHolder constructor.
     */
    public function __construct(?string $definitionId)
    {
        if ($definitionId === '') {
            throw new InvalidArgumentException('definitionId could not be empty.');
        }

        $this->value = $definitionId;
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?string
    {
        return $this->value;
    }
}
