<?php

declare(strict_types=1);

namespace vinyl\di\factory;

use Exception;
use vinyl\di\ClassMaterializer;
use vinyl\di\EvalClassMaterializer;
use vinyl\di\factory\compiler\ValueRenderer;
use vinyl\di\factory\compiler\ValueRendererCompositor;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\Map;
use function class_exists;
use function count;
use function implode;
use function ltrim;
use function md5;
use function sprintf;
use function var_export;
use function vinyl\di\class_extract_short_name_and_namespace;
use const PHP_EOL;

/**
 * Class FactoryPerServiceCompiler
 */
final class FactoryPerServiceCompiler implements Compiler
{
    private ClassMaterializer $classMaterializer;
    private ValueRenderer $valueRenderer;

    /**
     * FactoryPerServiceCompiler constructor.
     */
    public function __construct(
        ?ClassMaterializer $classMaterializer = null,
        ?ValueRenderer $valueRenderer = null
    ) {
        $this->classMaterializer = $classMaterializer ?? new EvalClassMaterializer();
        $this->valueRenderer = $valueRenderer ?? new ValueRendererCompositor();
    }

    /**
     * {@inheritDoc}
     */
    public function compile(string $factoryClassName, Map $factoryMetadataMap): ClassObject
    {
        if (class_exists($factoryClassName)) {
            throw new CompilerException("Factory [{$factoryClassName}] already exists.");
        }

        $mainFactoryClassName = ltrim($factoryClassName, '\\');
        $services = [];

        [$shortClassName, $namespace] = class_extract_short_name_and_namespace($mainFactoryClassName);
        try {
            /** @var \vinyl\di\factory\FactoryMetadata $metadata */
            foreach ($factoryMetadataMap as $metadata) {
                //TODO replace it
                $class = 'class' . md5($metadata->id);//class mustn't start from a numeric symbol
                $callable = "{$mainFactoryClassName}\\{$class}::create";
                $services[] = "'$metadata->id' => " . "'$callable'," . PHP_EOL;

                $makeFactoryMethodBody = $this->makeFactoryMethodBody($metadata, $mainFactoryClassName);

                $factoryContent = <<<TEMPLATE
                declare(strict_types=1);
                
                namespace $mainFactoryClassName;
                use \Psr\Container\ContainerInterface;
                
                final class $class
                {
                    public static function create(?array \$arguments = null, ContainerInterface \$di)
                    {
                        $makeFactoryMethodBody
                    }
                }
                TEMPLATE;

                $this->classMaterializer->materialize("{$mainFactoryClassName}\\{$class}", $factoryContent);
            }

            $mainFactoryContent = self::renderMainFactory(
                $namespace !== '' ? "namespace {$namespace};" : '',
                $shortClassName, implode('', $services)
            );

            $this->classMaterializer->materialize($mainFactoryClassName, $mainFactoryContent);
        } catch (Exception $e) {
            throw new CompilerException($e->getMessage(), 0, $e);
        }

        return ClassObject::create($factoryClassName);
    }

    /**
     * @param array<scalar> $arguments
     */
    private static function renderNewPattern(FactoryMetadata $metadata, array $arguments = []): string
    {
        $constructor = $metadata->constructor;
        $concatArguments = implode(',', $arguments);

        if ($constructor === null) {
            return "return new \\{$metadata->class}({$concatArguments});";
        }

        return "return \\{$constructor}({$concatArguments});";
    }

    private function makeFactoryMethodBody(FactoryMetadata $factoryMetadata, string $factoryClass): string
    {
        if (!$factoryMetadata->values) {
            return self::renderNewPattern($factoryMetadata);
        }

        $noArguments = $this->renderNoArguments($factoryMetadata);
        $withArguments = $this->renderWithArguments($factoryMetadata, $factoryClass);

        return "if(\$arguments === null){{$noArguments}}{$withArguments}";
    }

    private function renderNoArguments(FactoryMetadata $factoryMetadata): string
    {
        $missedVariables = [];
        $arguments = [];
        foreach ($factoryMetadata->values as $name => $value) {

            if ($value->isMissed()) {
                $missedVariables[] = $name;
                continue;
            }

            $arguments[] = $this->valueRenderer->render($value);
        }

        if (count($missedVariables) === 0) {
            return self::renderNewPattern($factoryMetadata, $arguments);
        }

        $missedVariablesString = implode(', ', $missedVariables);

        return "throw new \\vinyl\di\NotEnoughArgumentsPassedException"
            . "('To be able to instantiate this service "
            . "[$factoryMetadata->id] please provide next arguments [$missedVariablesString]');";
    }

    private function renderWithArguments(FactoryMetadata $factoryMetadata, string $factoryClass): string
    {
        $arguments = [];
        foreach ($factoryMetadata->values as $name => $value) {
            if ($value->isMissed()) {
                $arguments[] = sprintf(
                    '\array_key_exists(%s, $arguments) ? $arguments[%s] : \%s::throwException(%s, %s)',
                    var_export($name, true),
                    var_export($name, true),
                    $factoryClass,
                    var_export($factoryMetadata->id, true),
                    var_export($name, true)
                );
                continue;
            }

            $key = var_export($name, true);
            $arguments[] = "\$arguments[$key] ?? {$this->valueRenderer->render($value)}";
        }

        return self::renderNewPattern($factoryMetadata, $arguments);
    }

    private static function renderMainFactory(string $namespace, string $factoryName, string $services): string
    {
        return <<<MAIN_FACTRORY
        declare(strict_types=1);
        
        $namespace
        
        final class $factoryName implements \\vinyl\di\ObjectFactory, \\vinyl\di\ContainerAware
        {
            private const SERVICES = [
                $services
            ];
        
            /** @var \Psr\Container\ContainerInterface */
            private \$container;
        
            public function create(string \$id, ?array \$arguments = null): object
            {
                if (!isset(self::SERVICES[\$id])) {
                    throw new \\vinyl\di\NotFoundException(sprintf('[%s] not found.', \$id));
                }
        
                return self::SERVICES[\$id](\$arguments, \$this->container);
            }
        
            public function has(string \$id): bool
            {
                return isset(self::SERVICES[\$id]);
            }
        
            public function injectContainer(\\Psr\Container\ContainerInterface \$container): void
            {
                \$this->container = \$container;
            }
        
            public static function throwException(string \$definitionName, string \$paramName)
            {
                throw new \\vinyl\di\NotEnoughArgumentsPassedException(sprintf('[%s] require [%s] parameter.', \$definitionName, \$paramName));
            }
        }
        MAIN_FACTRORY;
    }
}
