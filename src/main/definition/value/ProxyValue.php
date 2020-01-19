<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

use InvalidArgumentException;

/**
 * Class ProxyValue
 */
final class ProxyValue implements \vinyl\di\definition\ProxyValue
{
    /** @var string|null */
    private ?string $definitionId;

    /**
     * ProxyValue constructor.
     */
    public function __construct(?string $definitionId = null)
    {
        if ($definitionId === '') {
            throw new InvalidArgumentException('Definition id could not be empty.');
        }

        $this->definitionId = $definitionId;
    }

    /**
     * {@inheritDoc}
     */
    public function value(): ?string
    {
        return $this->definitionId;
    }
}
