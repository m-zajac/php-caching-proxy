<?php

namespace MZ\Proxy\Behaviors;

use MZ\Proxy\ObjectProxy;
use MZ\Proxy\CallableProxy;

/**
 * Interface for Proxy behaviors
 */
abstract class AbstractBehavior
{
    /**
     * If this flag is true, ObjectProxy will create CallableProxy for each method.
     */
    public $distinct_for_methods = false;
    protected $namespace = '';
    protected $proxy;

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

    /**
     * Sets proxy
     * @param ObjectProxy|CallableProxy $proxy
     * @return AbstractBehavior
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Returns proxy
     * @return ObjectProxy|CallableProxy
     */
    public function getProxy()
    {
        return $this->proxy;
    }
}
