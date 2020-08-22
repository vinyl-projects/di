<?php

declare(strict_types=1);

namespace vinyl\di\definition\valueProcessor;

use vinyl\di\AliasDefinition;
use vinyl\di\ClassMaterializer;
use vinyl\di\ClassMaterializerException;
use vinyl\di\Definition;
use vinyl\di\definition\ClassResolver;
use vinyl\di\definition\ClassResolverAware;
use vinyl\di\definition\ClassResolverException;
use vinyl\di\definition\constructorMetadata\ConstructorValue;
use vinyl\di\definition\DefinitionDependency;
use vinyl\di\definition\DefinitionValue;
use vinyl\di\definition\IncompatibleTypeException;
use vinyl\di\definition\Lifetime;
use vinyl\di\definition\LifetimeResolver;
use vinyl\di\definition\ProxyValue;
use vinyl\di\definition\UnmodifiableDefinitionMap;
use vinyl\di\definition\value\StringValue;
use vinyl\di\definition\ValueProcessor;
use vinyl\di\definition\ValueProcessorException;
use vinyl\di\definition\ValueProcessorResult;
use vinyl\di\EvalClassMaterializer;
use vinyl\di\factory\argument\DefinitionFactoryValue;
use vinyl\di\proxy\LazyLoadingValueHolderProxyGenerator;
use vinyl\di\proxy\ProxyGenerator;
use vinyl\di\proxy\ProxyGeneratorException;
use vinyl\di\ShadowClassDefinition;
use vinyl\std\lang\ClassObject;
use function assert;
use function class_exists;
use function crc32;
use function interface_exists;
use function sprintf;
use function str_replace;
use function vinyl\std\lang\collections\vectorOf;

/**
 * Class ProxyValueProcessor
 */
final class ProxyValueProcessor implements ValueProcessor, ClassResolverAware
{
    private ?ClassResolver $classResolver = null;
    private ProxyGenerator $proxyGenerator;
    private ClassMaterializer $classMaterializer;
    private LifetimeResolver $lifetimeResolver;

    /**
     * ProxyValueProcessor constructor.
     */
    public function __construct(
        LifetimeResolver $lifetimeResolver,
        ?ClassMaterializer $classMaterializer = null,
        ?ProxyGenerator $proxyGenerator = null
    ) {
        $this->classMaterializer = $classMaterializer ?? new EvalClassMaterializer();
        $this->proxyGenerator = $proxyGenerator ?? new LazyLoadingValueHolderProxyGenerator();
        $this->lifetimeResolver = $lifetimeResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function process(
        DefinitionValue $value,
        ConstructorValue $constructorValue,
        UnmodifiableDefinitionMap $definitionMap
    ): ValueProcessorResult {
        assert($value instanceof ProxyValue);
        assert($this->classResolver !== null);

        $definitionId = $value->value();

        if ($definitionId === null) {
            $definitionId = $constructorValue->type();
        }

        $definition = self::resolveProxyDefinition($definitionMap, $definitionId);

        try {
            $classObject = $this->classResolver->resolve($definition, $definitionMap);
        } catch (ClassResolverException $e) {
            throw new ValueProcessorException(
                "An error occurred during class resolving. {$e->getMessage()}"
            );
        }

        $type = $constructorValue->type();
        $className = $classObject->name();
        if (!is_a($className, $type, true) && $type !== 'object' && $type !== 'mixed') {
            throw IncompatibleTypeException::create($type, "{$className} -> {$definitionId}");
        }

        try {
            $proxy = $this->proxyGenerator->generate($classObject);
        } catch (ProxyGeneratorException $e) {
            throw new ValueProcessorException(
                "An error occurred during proxy generation. {$e->getMessage()}"
            );
        }

        if (!class_exists($proxy->className, false)) {
            try {
                $this->classMaterializer->materialize($proxy->className, $proxy->classContent);
            } catch (ClassMaterializerException $e) {
                throw new ValueProcessorException("An error occurred during proxy materialization. {$e->getMessage()}");
            }
        }
        $lifetime = $this->lifetimeResolver->resolve($definition, $definitionMap);
        $proxyDefinition = self::createProxyDefinition($proxy->className, $definition, $lifetime);
        $proxiedDefinition = self::resolveDefinition($className, $definitionMap);

        return new ValueProcessorResult(
            new DefinitionFactoryValue($proxyDefinition->id(), false),
            vectorOf(
                DefinitionDependency::create($proxyDefinition),
                DefinitionDependency::createDetachedDependency($proxiedDefinition)
            )
        );
    }

    private static function resolveDefinition(string $typeValue, UnmodifiableDefinitionMap $definitionMap): Definition
    {
        if ($definitionMap->contains($typeValue)) {
            return $definitionMap->get($typeValue);
        }

        return ShadowClassDefinition::resolveShadowDefinition($typeValue, $definitionMap);
    }

    /**
     * @throws \vinyl\di\definition\ValueProcessorException
     */
    private static function resolveProxyDefinition(
        UnmodifiableDefinitionMap $definitionMap,
        string $definitionId
    ): Definition {
        if ($definitionMap->contains($definitionId)) {
            return $definitionMap->get($definitionId);
        }

        if (!class_exists($definitionId)) {
            if (interface_exists($definitionId)) {
                throw new ValueProcessorException("Could not create proxy. Implementation for [{$definitionId}] interface not registered.");
            }

            throw new ValueProcessorException("Could not create proxy. Class [{$definitionId}] not exists.");
        }

        return ShadowClassDefinition::resolveShadowDefinition($definitionId, $definitionMap);
    }

    private static function createProxyDefinition(
        string $proxyClassName,
        Definition $definition,
        Lifetime $lifetime
    ): AliasDefinition {
        #todo handle class names like 'Interface___'
        $proxyId = sprintf(
            '%s.auto.generated.proxy%s',
            mb_strtolower(str_replace(['\\', '_'], '.', $definition->id())),
            crc32($definition->id())
        );

        $proxyDefinition = new AliasDefinition($proxyId, ClassObject::create($proxyClassName));
        $proxyDefinition->changeLifetime($lifetime);
        #todo create ProxyDefinition and move this logic to it
        $proxyDefinition->argumentValues()->put(
            ProxyGenerator::PROXY_ARGUMENT_NAME,
            new StringValue($definition->id())
        );

        return $proxyDefinition;
    }

    /**
     * {@inheritDoc}
     */
    public function injectClassResolver(ClassResolver $resolver): void
    {
        $this->classResolver = $resolver;
    }
}
