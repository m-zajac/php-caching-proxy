<?php

namespace MZ\Proxy\Behaviors\Caching\Backend;

/**
 * Interface for Proxy backends
 */
interface BackendInterface
{
    /**
     * Stores value for givent arguments
     * @param string $namespace
     * @param array $arguments
     * @param mixed $value
     * @param int $timout [seconds]
     * @return Interface
     */
    public function set($namespace, array $arguments, $value, $timeout = 0);

    /**
     * Unsets value for given arguments
     * @param string $namespace
     * @param array $arguments
     * @return Interface
     */
    public function clear($namespace, array $arguments);

    /**
     * Returns value for given arguments. If key is invalid, returns null.
     * @param string $namespace
     * @param array $arguments
     * @return mixed
     */
    public function get($namespace, array $arguments);
}
