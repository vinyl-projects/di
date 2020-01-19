<?php

declare(strict_types=1);

namespace vinyl\di\definition\value;

use LogicException;
use vinyl\di\definition\DefinitionValue;

/**
 * Class NoValue
 */
final class NoValue implements DefinitionValue
{
    private static ?NoValue $instance = null;

    /**
     * @inheritDoc
     */
    public function value()
    {
        throw new LogicException('NoValue object is an indicator that value are not provided. Calling value() method makes no sense.');
    }

    public static function get(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
