<?php

declare(strict_types=1);

namespace vinyl\diBenchmark\metadataBuilder;

use vinyl\di\EvalClassMaterializer;
use function array_chunk;
use function range;

/**
 * Class AssetGenerator
 */
class AssetGenerator
{
    private const CLASS_DEPENDENCY_COUNT = 10;
    private EvalClassMaterializer $classMaterializer;

    /**
     * AssetGenerator constructor.
     */
    public function __construct(?EvalClassMaterializer $classMaterializer = null)
    {
        $this->classMaterializer = $classMaterializer ?? new EvalClassMaterializer();
    }

    /**
     * @return string[]
     */
    public function generate(?int $totalClassCount = null): array
    {
        $totalClassCount = $totalClassCount ?? 1000;

        $classToRegister = [];
        $range = range(1, $totalClassCount);
        foreach (array_chunk($range, self::CLASS_DEPENDENCY_COUNT) as $chunk) {
            $classNames = [];
            foreach ($chunk as $item) {
                $classNames[] = "PerformanceGeneratedClass{$item}";
            }

            for ($i = 0; $i < self::CLASS_DEPENDENCY_COUNT; $i++) {
                $className = $classNames[$i];
                $requiredClass = '';

                if (isset($classNames[$i + 1])) {
                    $requiredClass = "\\{$classNames[$i + 1]} \$class";
                }

                $classContent = <<<TEMPLATE
                class $className {
                    public function __construct($requiredClass) { }
                }
                TEMPLATE;

                $this->classMaterializer->materialize($className, $classContent);
            }

            $classToRegister[] = $classNames[0];
        }

        return $classToRegister;
    }
}
