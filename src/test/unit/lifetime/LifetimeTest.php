<?php

declare(strict_types=1);

namespace vinyl\diTest\unit\lifetime;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use vinyl\di\definition\Lifetime;
use function get_class;
use function serialize;
use function strlen;
use function unserialize;

/**
 * Class LifetimeTest
 */
abstract class LifetimeTest extends TestCase
{
    /**
     * @test
     */
    public function exceptionIsThrowOnSerialize(): void
    {
        $this->expectException(RuntimeException::class);
        $lifetime = $this->createLifetime();
        serialize($lifetime);
    }

    /**
     * @test
     */
    public function exceptionIsThrownOnClone(): void
    {
        $this->expectException(RuntimeException::class);
        $lifetime = $this->createLifetime();
        /** @noinspection PhpExpressionResultUnusedInspection */
        clone $lifetime;
    }

    /**
     * @test
     */
    public function exceptionThrownOnUnserialize(): void
    {
        $this->expectException(RuntimeException::class);
        $lifetime = $this->createLifetime();
        $className = get_class($lifetime);
        $length = strlen($className);
        unserialize("O:{$length}:\"{$className}\":0:{}");
    }

    abstract protected function createLifetime(): Lifetime;
}
