<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\abstractContainerTest\preferenceNotOverrideArguments;

/**
 * Interface A
 */
interface A
{
    public function name(): string;

    public function surname(): string;
}
