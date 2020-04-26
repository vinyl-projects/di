<?php

declare(strict_types=1);

namespace vinyl\di\proxy;

use Laminas\Code\Generator\AbstractMemberGenerator;
use ProxyManager\Generator\ClassGenerator;
use ProxyManager\ProxyGenerator\LazyLoadingValueHolderGenerator;
use Psr\Container\ContainerInterface;
use vinyl\std\lang\ClassObject;

/**
 * Class LazyLoadingValueHolderProxyGenerator
 */
final class LazyLoadingValueHolderProxyGenerator implements ProxyGenerator
{
    private LazyLoadingValueHolderGenerator $proxyGenerator;

    /**
     * ProxyGenerator constructor.
     */
    public function __construct(?LazyLoadingValueHolderGenerator $proxyGenerator = null)
    {
        $this->proxyGenerator = $proxyGenerator ?? new LazyLoadingValueHolderGenerator();
    }

    /**
     * {@inheritDoc}
     */
    public function generate(ClassObject $class): ProxyGeneratorResult
    {
        $originalClass = $class->toReflectionClass();
        $shortClassName = $originalClass->getShortName();
        $proxyName = "{$class->className()}\\{$shortClassName}_AutoGeneratedProxy";
        $classGenerator = new ClassGenerator($proxyName);
        $this->proxyGenerator->generate($originalClass, $classGenerator);
        $classGenerator->removeMethod('staticProxyConstructor');
        $classGenerator->removeMethod('__construct');

        $proxyArgumentName = ProxyGenerator::PROXY_ARGUMENT_NAME;
        $classGenerator->addMethod(
            '__construct',
            [
                ['name' => 'di', 'type' => ContainerInterface::class],
                ['name' => $proxyArgumentName, 'type' => 'string'],
            ],
            AbstractMemberGenerator::FLAG_PUBLIC,
            "\$this->setProxyInitializer(function (&\$wrappedObject, \$proxy, \$method, \$parameters, &\$initializer) use (\$di, \${$proxyArgumentName}) {
                \$wrappedObject = \$di->get(\${$proxyArgumentName});
                \$initializer   = null;
            });"
        );

        return new ProxyGeneratorResult($proxyName, $classGenerator->generate());
    }
}
