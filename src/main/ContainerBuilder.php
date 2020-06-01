<?php

declare(strict_types=1);

namespace vinyl\di;

use LogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use vinyl\di\definition\CacheableClassResolver;
use vinyl\di\definition\DefinitionMap;
use vinyl\di\definition\ProxyValue;
use vinyl\di\definition\RecursionFreeClassResolver;
use vinyl\di\definition\RecursionFreeLifetimeResolver;
use vinyl\di\definition\RecursiveDefinitionTransformer;
use vinyl\di\definition\valueProcessor\ProxyValueProcessor;
use vinyl\di\definition\valueProcessor\ValueProcessorCompositor;
use vinyl\di\factory\DefinitionMapTransformer;
use vinyl\di\factory\FactoryPerServiceCompiler;
use function assert;

/**
 * Class ContainerBuilder
 */
final class ContainerBuilder
{
    private const FILESYSTEM_MATERIALIZER = 'filesystem';
    private const EVAL_MATERIALIZER       = 'eval';
    private const DEVELOPER_FACTORY       = 'developer';
    private const COMPILED_FACTORY        = 'compiled';

    private LoggerInterface $logger;
    private ?DefinitionMap $definitionMap;
    private ?string $materializer = null;
    private ?string $factory = null;
    private bool $composerPluginRegister = false;
    private string $factoryClassName;
    private string $lifetimeMapName;
    private bool $isUsed = false;

    private function __construct(DefinitionMap $definitionMap, ?LoggerInterface $logger)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->definitionMap = $definitionMap;
    }

    public static function create(DefinitionMap $definitionMap, ?LoggerInterface $logger = null): self
    {
        return new self($definitionMap, $logger);
    }

    public function useFileSystemMaterializerStrategy(string $pathToAutoloadableFolder): self
    {
        $this->throwIfBuilderAlreadyUsed();
        if ($this->materializer !== null) {
            throw new LogicException('Materializer already set.');
        }

        $this->materializer = self::FILESYSTEM_MATERIALIZER;

        return $this;
    }

    public function useEvalMaterializerStrategy(): self
    {
        $this->throwIfBuilderAlreadyUsed();
        if ($this->materializer !== null) {
            throw new LogicException('Materializer already set.');
        }

        $this->materializer = self::EVAL_MATERIALIZER;

        return $this;
    }

    public function useDeveloperFactory(): self
    {
        $this->throwIfBuilderAlreadyUsed();
        if ($this->factory !== null) {
            throw new LogicException('Factory already set.');
        }

        $this->factory = self::DEVELOPER_FACTORY;

        return $this;
    }

    public function useCompiledFactory(string $factoryClassName, string $lifetimeMapName): self
    {
        $this->throwIfBuilderAlreadyUsed();
        if ($this->factory !== null) {
            throw new LogicException('Factory already set.');
        }

        $this->factory = self::COMPILED_FACTORY;
        $this->factoryClassName = $factoryClassName;
        $this->lifetimeMapName = $lifetimeMapName;

        return $this;
    }

    public function useComposerPluginRegister(): self
    {
        $this->throwIfBuilderAlreadyUsed();
        $this->composerPluginRegister = true;

        return $this;
    }

    /**
     * @throws \vinyl\di\definition\ClassCircularReferenceFoundException
     * @throws \vinyl\di\definition\DefinitionTransformerException
     * @throws \vinyl\di\factory\CompilerException
     */
    public function build(): Container
    {
        $this->throwIfBuilderAlreadyUsed();
        assert($this->definitionMap !== null);

        if ($this->factory === null) {
            throw new LogicException('Factory not chosen.');
        }

        if ($this->materializer === null) {
            throw new LogicException('Materializer not chosen.');
        }

        $materializer = $this->resolveMaterializer();

        $classResolver = new CacheableClassResolver(new RecursionFreeClassResolver());
        $lifetimeResolver = new RecursionFreeLifetimeResolver();
        $processorMap = [
            ProxyValue::class => new ProxyValueProcessor($lifetimeResolver, $materializer),
        ];
        $valueProcessor = new ValueProcessorCompositor($processorMap, $classResolver);
        $definitionTransformer = new RecursiveDefinitionTransformer($valueProcessor, $classResolver, $lifetimeResolver);

        if ($this->factory === self::DEVELOPER_FACTORY) {
            $lifetime = new ModifiableLifetimeCodeMap([]);
            $factory = new DeveloperFactory($this->definitionMap, $lifetime, $definitionTransformer);
            $this->definitionMap = null;
            $this->isUsed = true;

            return new Container($lifetime, $factory);
        }

        $factoryMetadataMap = (new DefinitionMapTransformer($definitionTransformer))->transform($this->definitionMap);
        $factoryCompiler = new FactoryPerServiceCompiler($materializer);
        $factoryClassName = $this->factoryClassName;
        $factoryClass = $factoryCompiler->compile($factoryClassName, $factoryMetadataMap);
        $objectFactory = $factoryClass->toReflectionClass()->newInstanceWithoutConstructor();
        assert($objectFactory instanceof ObjectFactory);

        $lifetimeMapCompiler = new LifetimeMapCompiler($materializer);
        $lifetimeMapClassObject = $lifetimeMapCompiler->compile($this->lifetimeMapName, $factoryMetadataMap);
        $lifetimeCodeMap = $lifetimeMapClassObject->toReflectionClass()->newInstanceWithoutConstructor();
        assert($lifetimeCodeMap instanceof LifetimeCodeMap);

        $this->isUsed = true;
        $this->definitionMap = null;

        return new Container($lifetimeCodeMap, $objectFactory);
    }

    private function resolveMaterializer(): EvalClassMaterializer
    {
        if ($this->materializer === self::EVAL_MATERIALIZER) {
            return new EvalClassMaterializer();
        }

        throw new \RuntimeException('Not implemented.');
    }

    private function throwIfBuilderAlreadyUsed(): void
    {
        if (!$this->isUsed) {
            return;
        }

        throw new \LogicException(self::class . ' could be used only once. Please create new one.');
    }
}
