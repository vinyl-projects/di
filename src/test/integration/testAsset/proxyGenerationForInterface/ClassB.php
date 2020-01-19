<?php
declare(strict_types=1);


namespace vinyl\diTest\integration\testAsset\proxyGenerationForInterface;


class ClassB implements InterfaceB
{
    public function test(): string
    {
        return 'Hello world';
    }
}
