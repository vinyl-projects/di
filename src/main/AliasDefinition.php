<?php

declare(strict_types=1);

namespace vinyl\di;

use InvalidArgumentException;
use RuntimeException;
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
abstract class AliasDefinition extends AbstractDefinition
{
    protected static function validateAliasId(string $id): void
    {
        $pregMatchResult = @preg_match('/^[a-z0-9](?:\.?[a-z0-9])*$/', $id, $matches);

        if ($pregMatchResult === 1) {
            return;
        }

        if ($pregMatchResult !== false) {
            throw new InvalidArgumentException("Invalid alias id provided [{$id}].");
        }

        $input = get_defined_constants(true)['pcre'];
        assert(is_array($input));
        $definedErrorConstants = array_filter(
            $input,
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
