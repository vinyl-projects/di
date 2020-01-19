<?php

declare(strict_types=1);

namespace vinyl\di\definition;

use RuntimeException;
use function get_class;

/**
 * Class PrototypeLifetime
 */
final class PrototypeLifetime implements Lifetime
{
    private static ?PrototypeLifetime $instance = null;

    /**
     * PrototypeLifetime constructor.
     */
    private function __construct()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function code(): string
    {
        return 'prototype';
    }

    /**
     * Returns singleton instance of lifetime
     */
    public static function get(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __clone()
    {
        throw new RuntimeException(get_class($this) . ' is not cloneable object.');
    }

    /**
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        throw new RuntimeException(get_class($this) . ' is not serializable object.');
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        throw new RuntimeException(get_class($this) . ' is not unserializable object.');
    }
}
