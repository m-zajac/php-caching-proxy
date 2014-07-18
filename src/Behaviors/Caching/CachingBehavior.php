<?php

namespace MZ\Proxy\Behaviors\Caching;

use MZ\Proxy\Behaviors\BehaviorInterface;
use MZ\Proxy\Behaviors\Caching;

/**
 * CachingBehavior
 */
class CachingBehavior implements BehaviorInterface
{
    protected $backend;
    protected $key_generator;
    protected $serializer;
    protected $timeout = 0;

    /**
     * Constructor
     * @param string $cache_key
     * @param Caching\Backend\BackendInterface $backend
     * @param Caching\Serializer\SerializerInterface $serializer
     * @param Caching\KeyGenerator\KeyGeneratorInterface $key_generator
     */
    public function __construct(
        $cache_key = null,
        Caching\Backend\BackendInterface $backend = null,
        Caching\Serializer\SerializerInterface $serializer = null,
        Caching\KeyGenerator\KeyGeneratorInterface $key_generator = null
    ) {
        $this->backend = $backend;
        $this->key_generator = $key_generator;
        $this->serializer = $serializer;
    }

    /**
     * Invokes callable
     */
    public function invoke($callable, array $arguments)
    {
        if (!$this->cache_key) {
            throw new Exceptions\Exception('Cache key is not set');
        }

        // create cache key for arguments
        $cache_key = $this->key_generator->generate($this->cache_key, $arguments);

        // try getting result from cache
        $result = $this->backend->get($cache_key);
        if ($result !== false) {
            return $this->serializer->unserialize($result);
        }

        // invoke callable
        $result = call_user_func_array($callable, $arguments);

        // store result
        $this->backend->set(
            $cache_key,
            $this->serializer->serialize($result),
            $this->timeout
        );

        return $result;
    }

    /**
     * Sets cache key
     * @param string $key
     * @return CallableProxy
     */
    public function setCacheKey($key)
    {
        $this->cache_key = $key;

        return $this;
    }

    /**
     * Returns cache key
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cache_key;
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

    /**
     * Sets serializer
     * @param Caching\Serializer\SerializerInterface $serializer
     * @return CallableProxy
     */
    public function setSerializer(Caching\Serializer\SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Returns serializer
     * @return Caching\Serializer\SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Sets key generator
     * @param Caching\KeyGenerator\KeyGeneratorInterface $key_generator
     * @return CallableProxy
     */
    public function setKeyGenerator(Caching\KeyGenerator\KeyGeneratorInterface $key_generator)
    {
        $this->key_generator = $key_generator;

        return $this;
    }

    /**
     * Returns key generator
     * @return Caching\KeyGenerator\KeyGeneratorInterface
     */
    public function getKeyGenerator()
    {
        return $this->key_generator;
    }
}
