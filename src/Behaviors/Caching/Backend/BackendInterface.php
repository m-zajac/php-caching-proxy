<?php

namespace MZ\Proxy\Behaviors\Caching\Backend;

/**
 * Interface for Proxy backends
 */
interface BackendInterface
{
    /**
     * Stores value for givent key
     * @param string $key
     * @param mixed $value
     * @param int $timout [seconds]
     * @return Interface
     */
    public function set($key, $value, $timeout = 0);

    /**
     * Unsets value for given key
     * @param string $key
     * @return Interface
     */
    public function clear($key);

    /**
     * Returns value for given key. If key is invalid, returns null.
     * @param string $key
     * @return mixed
     */
    public function get($key);
}
