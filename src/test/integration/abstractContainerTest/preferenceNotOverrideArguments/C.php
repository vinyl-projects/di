<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\abstractContainerTest\preferenceNotOverrideArguments;

/**
 * Class C
 */
class C extends B
{

    public function name(): string
    {
        return 'Preference - ' . parent::name();
    }

    public function surname(): string
    {
        return 'Preference - ' . parent::surname();
    }
}
