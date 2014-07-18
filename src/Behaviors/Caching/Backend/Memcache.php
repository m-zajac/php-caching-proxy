<?php

namespace MZ\Proxy\Behaviors\Caching\Backend;

/**
 * Memcache proxy backend. Uses \Memcache class.
 */
class Memcache implements BackendInterface
{
    protected $storage;
    protected $prefix;

    /**
     * Constructor
     * @param string $prefix Memcache key prefix
     * @param \Memcache Memcache instance [optional]
     */
    public function __construct($prefix, \Memcache $memcache = null)
    {
        if (!$memcache) {
            $memcache = new \Memcache;
        }

        $this->storage = $memcache;
    }

    /**
     * Adds memcache serwer.
     * @see http://php.net/manual/en/memcache.addserver.php
     * @return true on success, false on failure
     */
    public function addServer()
    {
        $args = func_get_args();
        return call_user_func_array(
            array($this->storage, 'addServer'),
            $args
        );
    }

    /**
     * Gets memcache serwer status.
     * @see http://php.net/manual/en/memcache.getserverstatus.php
     * @return 0 on failure, 1 on success
     */
    public function getServerStatus()
    {
        $args = func_get_args();
        return call_user_func_array(
            array($this->storage, 'getServerStatus'),
            $args
        );
    }

    public function set($namespace, array $arguments, $value, $timeout = 0)
    {
        $key = $this->getKeyFromArguments($namespace, $arguments);
        $timeout_time = 0;
        if ($timeout) {
            $timeout_time = time() + (int)$timeout;
        }

        $this->storage->set(
            $key,
            serialize($value),
            null,
            $timeout_time
        );

        return $this;
    }

    public function clear($namespace, array $arguments)
    {
        $key = $this->getKeyFromArguments($namespace, $arguments);
        $this->storage->delete($key);

        return $this;
    }

    public function get($namespace, array $arguments)
    {
        $key = $this->getKeyFromArguments($namespace, $arguments);
        $result = $this->storage->get($key);
        if ($result === false) {
            return null;
        }

        return unserialize($result);
    }

    protected function getKeyFromArguments($namespace, array $arguments)
    {
        $key = '';
        foreach ($arguments as $arg) {
            $key .= serialize($arg);
        }

        return 'mzp'.$this->prefix.$namespace.$key;
    }
}
