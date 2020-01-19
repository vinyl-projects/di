<?php

declare(strict_types=1);

namespace vinyl\diTest\integration\testAsset\scoped;

class ServiceB
{
    public $serviceA;

    public function __construct(ServiceA $serviceA)
    {
        $this->serviceA = $serviceA;
    }
}
