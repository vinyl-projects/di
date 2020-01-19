<?php


namespace vinyl\diTest\integration\testAsset\noConflictInProxyNameAndAlias;


class ClassA
{
    public \vinyl\diTest\integration\testAsset\noConflictInProxyNameAndAlias\ClassB $b;
    public \vinyl\diTest\integration\testAsset\noConflictInProxyNameAndAlias\ClassB $c;

    public function __construct($b, $c)
    {
        $this->b = $b;
        $this->c = $c;
    }
}
