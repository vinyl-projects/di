<?php

declare(strict_types=1);

namespace vinyl\di;

use InvalidArgumentException;
use RuntimeException;
use vinyl\std\lang\ClassObject;
use function array_filter;
use function array_flip;
use function get_defined_constants;
use function preg_last_error;
use function preg_match;
use function strrpos;
use const ARRAY_FILTER_USE_KEY;

/**
 * Class AliasDefinition
 */
final class AliasDefinition extends AbstractDefinition
{
    /**
     * AliasDefinition constructor.
     */
    public function __construct(string $id, ClassObject $class)
    {
        self::validateAliasId($id);
        parent::__construct($id, $class);
    }

    public static function validateAliasId(string $id): void
    {
        $pregMatchResult = @preg_match('/^[a-z0-9](?:\.?[a-z0-9])*$/', $id, $matches);

        if ($pregMatchResult === 1) {
            return;
        }

        if ($pregMatchResult !== false) {
            throw new InvalidArgumentException("Invalid alias id provided [{$id}].");
        }

        $definedErrorConstants = array_filter(
            get_defined_constants(true)['pcre'],
            static fn(string $constName): bool => strrpos($constName, '_ERROR', -1) !== false,
            ARRAY_FILTER_USE_KEY
        );

        $codeToNameMap = array_flip($definedErrorConstants);
        $errorCode = preg_last_error();
        $constantName = $codeToNameMap[$errorCode];

        throw new RuntimeException(
            "An error occurred during alias id validation. Alias: {$id}. Error code: {$errorCode} - {$constantName}"
        );
    }
}
