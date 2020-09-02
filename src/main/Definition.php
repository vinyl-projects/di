<?php

declare(strict_types=1);

namespace vinyl\di;

use vinyl\di\definition\Instantiator;
use vinyl\di\definition\Lifetime;
use vinyl\std\lang\ClassObject;
use vinyl\std\lang\collections\MutableMap;

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
     * Returns {@see Instantiator} instance that holds callable string that must be used to instantiate new object
     * If null is returned, default constructor must be used
     */
    public function instantiator(): ?Instantiator;

    /**
     * Change instantiator
     */
    public function changeInstantiator(?Instantiator $objectInstantiator): void;

    /**
     * Changes definition lifetime
     */
    public function changeLifetime(?Lifetime $lifetime): void;

    /**
     * Returns definition lifetime
     */
    public function lifetime(): ?Lifetime;

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
     */
    public function classObject(): ClassObject;

    /**
     * Replaces the class for current definition instance
     *
     * @return ClassObject Previous class object
     */
    public function replaceClass(ClassObject $newCLass): ClassObject;

    /**
     * Returns argument values
     *
     * @return MutableMap<string, \vinyl\di\definition\DefinitionValue>
     */
    public function argumentValues(): MutableMap;
}
