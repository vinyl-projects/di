<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\di\definition\Lifetime;
use vinyl\di\definition\ValueMap;

/**
 * Interface Definition
 *
 * @todo introduce new RootDefinition interface with className and replaceClass methods ???
 */
interface Definition
{
    /**
     * Returns unique id of definition
     */
    public function id(): string;

    /**
     * Returns static method name which should be used as constructor
     * If null is returned, default constructor must be used
     */
    public function constructorMethodName(): ?string;

    /**
     * Change constructor method
     *
     * The new method must be 'public' and 'static'
     * Pay attention that validation will not be performed during method set, it will be triggered lately during factory building for
     * this definition
     */
    public function changeConstructorMethod(string $methodName): void;

    /**
     * Changes definition lifetime
     */
    public function changeLifetime(Lifetime $lifetime): void;

    /**
     * Returns lifetime of definition
     */
    public function lifetime(): Lifetime;

    /**
     * Checks whether argument inheritance enabled
     *
     * If argument inheritance for definition is enabled {@see \vinyl\di\definition\ValueCollector}
     * will merge arguments of current definition with parent definition
     */
    public function isArgumentInheritanceEnabled(): bool;

    /**
     * Toggles argument inheritance status
     *
     * @return bool|null Returns previous value or null if value not changed
     */
    public function toggleArgumentInheritance(bool $status): ?bool;

    /**
     * Returns class name of definition
     *
     * @return string
     */
    public function className(): string;

    /**
     * Replaces the class for current definition instance
     *
     * @return string An old class
     */
    public function replaceClass(string $newCLass): string;

    /**
     * Returns argument values
     */
    public function argumentValues(): ValueMap;
}
