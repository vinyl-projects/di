<?php

declare(strict_types=1);

namespace vinyl\di;

use Exception;
use InvalidArgumentException;
use ParseError;
use function class_exists;
use function error_clear_last;
use function error_get_last;

/**
 * Class EvalClassMaterializer
 */
final class EvalClassMaterializer implements ClassMaterializer
{
    /**
     * {@inheritDoc}
     */
    public function materialize(string $className, string $classContent): void
    {
        if ($className === '') {
            throw new InvalidArgumentException('Class name could not be empty.');
        }

        if ($classContent === '') {
            throw new InvalidArgumentException('Class content could not be empty.');
        }

        try {
            if (class_exists($className)) {
                throw new ClassMaterializerException("Could not materialize class [{$className}]. Class already exist.");
            }
        } catch (Exception $e) {
            //if throwable autoloader set
        }

        try {
            error_clear_last();
            @eval($classContent);
            $lastError = error_get_last();

            if ($lastError !== null) {
                throw new ClassMaterializerException("Could not materialize class [{$className}]. {$lastError['message']}");
            }
        } catch (ParseError $e) {
            throw new ClassMaterializerException(
                "Could not materialize class [{$className}]. {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }

        if (!class_exists($className)) {
            throw new ClassMaterializerException("Something went wrong while [{$className}] class materialization.");
        }
    }
}
