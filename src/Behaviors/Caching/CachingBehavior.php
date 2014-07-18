<?php

namespace MZ\Proxy\Behaviors\Caching;

use MZ\Proxy\Behaviors\AbstractBehavior;
use MZ\Proxy\Behaviors\Caching;

/**
 * CachingBehavior
 */
class CachingBehavior extends AbstractBehavior
{
    protected $backend;
    protected $timeout = 0;

    /**
     * Constructor
     * @param int $timeout
     * @param string $cache_namespace
     * @param Caching\Backend\BackendInterface $backend
     */
    public function __construct(
        $timeout = 0,
        Caching\Backend\BackendInterface $backend = null
    ) {
        $this->timeout = $timeout;
        $this->backend = $backend;
    }

    /**
     * Invokes callable
     */
    public function invoke($callable, array $arguments)
    {
        // try getting result from cache
        $result = $this->backend->get($this->getNamespace(), $arguments);
        if ($result !== null) {
            return $result;
        }

        // invoke callable
        $result = call_user_func_array($callable, $arguments);

        // store result
        $this->backend->set(
            $this->getNamespace(),
            $arguments,
            $result,
            $this->timeout
        );

        return $result;
    }

    /**
     * Sets timeout
     * @param int $value
     * @return CallableProxy
     */
    public function setTimeout($value)
    {
        $this->timeout = $value;

        return $this;
    }

    /**
     * Returns timeout
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Sets backend
     * @param Caching\Backend\BackendInterface $backend
     * @return CallableProxy
     */
    public function setBackend(Caching\Backend\BackendInterface $backend)
    {
        $this->backend = $backend;

        return $this;
    }

    /**
     * Returns backend
     * @return Caching\Backend\BackendInterface
     */
    public function getBackend()
    {
        return $this->backend;
    }
}
