<?php

namespace MZ\Proxy\Behaviors;

/**
 * Interface for Proxy behaviors
 */
abstract class AbstractBehavior
{
    protected $namespace = '';

    /**
     * Invokes callable
     * @param callable $callable
     * @param array $arguments
     * @return mixed
     */
    abstract public function invoke($callable, array $arguments);

    /**
     * Sets namespace
     * @param string $namespace
     * @return AbstractBehavior
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Returns namespace
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
