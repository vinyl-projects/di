<?php

declare(strict_types=1);

namespace vinyl\di\proxy;

use InvalidArgumentException;
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
    public function generate(ClassObject $class, string $proxyClassName): string
    {
        if ($proxyClassName === '') {
            throw new InvalidArgumentException('Proxy class name could not be empty.');
        }

        $originalClass = $class->toReflectionClass();
        $classGenerator = new ClassGenerator($proxyClassName);
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

        $classContent = $classGenerator->generate();
        assert(is_string($classContent));

        return $classContent;
    }
}
