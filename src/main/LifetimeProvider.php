<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\di\definition\SingletonLifetime;
use function array_replace;

/**
 * Class LifetimeProvider
 */
final class LifetimeProvider
{
    /** @var array<string, string> */
    private array $lifetimeMap;
    private string $defaultLifetimeCode;

    /**
     * ServiceLifeTimeStorage constructor.
     *
     * @param array<string, string> $lifetimeData
     */
    public function __construct(array $lifetimeData)
    {
        $this->lifetimeMap = $lifetimeData;
        $this->defaultLifetimeCode = SingletonLifetime::get()->code();
    }

    /**
     * Returns service lifetime code.
     *
     * If lifetime not available for given definition id, default lifetime code will be returned
     */
    public function get(string $definitionId): string
    {
        return $this->lifetimeMap[$definitionId] ?? $this->defaultLifetimeCode;
    }

    public function add(LifetimeProvider $lifetimeMap): void
    {
        $this->lifetimeMap = array_replace($this->lifetimeMap, $lifetimeMap->lifetimeMap);
    }
}
