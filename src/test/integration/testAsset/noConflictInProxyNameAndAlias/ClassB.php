<?php


namespace vinyl\diTest\integration\testAsset\noConflictInProxyNameAndAlias;


class ClassB
{
    private $id;

    /**
     * ClassB constructor.
     */
    public function __construct(string $id = 'default')
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
