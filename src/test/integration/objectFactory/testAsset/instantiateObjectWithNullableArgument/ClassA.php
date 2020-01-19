<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\objectFactory\testAsset\instantiateObjectWithNullableArgument;

/**
 * Class ClassA
 *
 * @package vinyl\diTest\integration\objectFactory\testAsset\instantiateObjectWithNullableArgument
 */
class ClassA
{
    public ?string $data;

    public function __construct(?string $data)
    {
        $this->data = $data;
    }
}
