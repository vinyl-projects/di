<?php

namespace PHPSTORM_META {

    override(\vinyl\di\ObjectFactory::create(0), map([]));
    override(\Psr\Container\ContainerInterface::get(0), map([]));

    registerArgumentsSet(
        'lifetimes',
        \vinyl\di\definition\PrototypeLifetime::get(),
        \vinyl\di\definition\ScopedLifetime::get(),
        \vinyl\di\definition\SingletonLifetime::get()
    );
    expectedArguments(\vinyl\di\definitionMapBuilder\DefinitionBuilder::lifetime(), 0, argumentsSet('lifetimes'));
    expectedArguments(\vinyl\di\Definition::changeLifetime(), 0, argumentsSet('lifetimes'));
}
