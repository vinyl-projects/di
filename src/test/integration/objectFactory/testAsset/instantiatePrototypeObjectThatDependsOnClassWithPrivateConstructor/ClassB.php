<?php

namespace vinyl\diTest\integration\objectFactory\testAsset\instantiatePrototypeObjectThatDependsOnClassWithPrivateConstructor;

class ClassB
{
    private function __construct()
    {
    }

    public static function create(): ClassB
    {
        return new self();
    }
}