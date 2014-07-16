<?php

namespace MZ\Proxy;

/**
 * Caching proxy for objects. Caches object methods.
 * Proxy methods start with "proxy".
 */
class ObjectProxy
{
    protected $object;
    protected $cache_key;
    protected $backend;
    protected $key_generator;
    protected $serializer;
    protected $default_timeout = 0;
    protected $method_proxies = array();

    /**
     * If array - only methods in array will be proxied. If null - all methods proxied.
     */
    protected $proxied_methods = null;

    /**
     * Constructor
     * @param object $object
     * @param string $cache_key
     * @param Backend\BackendInterface $backend
     * @param Serializer\SerializerInterface $serializer
     * @param KeyGenerator\KeyGeneratorInterface $key_generator
     */
    public function __construct(
        $object = null,
        $cache_key = null,
        Backend\BackendInterface $backend = null,
        Serializer\SerializerInterface $serializer = null,
        KeyGenerator\KeyGeneratorInterface $key_generator = null
    )
    {
        $this->object = $object;
        $this->cache_key = $cache_key;
        $this->backend = $backend;
        $this->key_generator = $key_generator;
        $this->serializer = $serializer;
    }

    /**
     * Invokes object method
     * @param string $name
     * @param array arguments
     */
    public function __call($name, array $arguments)
    {
        if ($this->proxied_methods !== null and !in_array($name, $this->proxied_methods)) {
            // skip proxy
            return call_user_func_array(array($this->object, $name), $arguments);
        }

        $proxy = $this->proxyGetMethodProxy($name);

        return call_user_func_array($proxy, $arguments);
    }

    /**
     * Returns object attribute
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->object->{$name};
    }

    /**
     * Sets object attribute
     * @param string $name
     * @return mixed
     */
    public function __set($name, $value)
    {
        $this->object->{$name} = $value;
    }

    /**
     * Returns 'isset' on objects attribute
     * @param string $name
     * @return mixed
     */
    public function __isset($name)
    {
        return isset($this->object->{$name});
    }

    /**
     * Unsets objects attribute
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->object->{$name});
    }

    /**
     * Returns string representation of proxied object
     * @return string
     */
    public function __toString()
    {
        return ''.$this->object;
    }

    /**
     * Sets proxied object
     * @param object $object
     * @return ObjectProxy
     */
    public function proxySetObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Returns callable
     * @return callable
     */
    public function proxyGetObject()
    {
        return $this->callable;
    }

    /**
     * Sets proxied methods
     * @param array|string $methods
     * @return ObjectProxy
     */
    public function proxySetMethods($methods)
    {
        if ($methods === null) {
            $this->proxied_methods = null;
        } else {
            $this->proxied_methods = (array)$methods;
        }

        return $this;
    }

    /**
     * Returns proxied methods
     * @return null|array
     */
    public function proxyGetMethods()
    {
        return $this->callable;
    }

    /**
     * Sets cache key
     * @param string $key
     * @return ObjectProxy
     */
    public function proxySetCacheKey($key)
    {
        $this->cache_key = $key;

        return $this;
    }

    /**
     * Returns cache key
     * @return string
     */
    public function proxyGetCacheKey()
    {
        return $this->cache_key;
    }

    /**
     * Sets default timeout
     * @param int $value
     * @return ObjectProxy
     */
    public function proxySetDefaultTimeout($value)
    {
        $this->default_timeout = $value;

        return $this;
    }

    /**
     * Returns default timeout
     * @return int
     */
    public function proxyGetDefaultTimeout()
    {
        return $this->default_timeout;
    }

    /**
     * Sets backend
     * @param Backend\BackendInterface $backend
     * @return ObjectProxy
     */
    public function proxySetBackend(Backend\BackendInterface $backend)
    {
        $this->backend = $backend;

        return $this;
    }

    /**
     * Returns backend
     * @return Backend\BackendInterface
     */
    public function proxyGetBackend()
    {
        return $this->backend;
    }

    /**
     * Sets serializer
     * @param Serializer\SerializerInterface $serializer
     * @return ObjectProxy
     */
    public function proxySetSerializer(Serializer\SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Returns serializer
     * @return Serializer\SerializerInterface
     */
    public function proxyGetSerializer()
    {
        return $this->serializer;
    }

    /**
     * Sets key generator
     * @param KeyGenerator\KeyGeneratorInterface $key_generator
     * @return ObjectProxy
     */
    public function proxySetKeyGenerator(KeyGenerator\KeyGeneratorInterface $key_generator)
    {
        $this->key_generator = $key_generator;

        return $this;
    }

    /**
     * Returns key generator
     * @return KeyGenerator\KeyGeneratorInterface
     */
    public function proxyGetKeyGenerator()
    {
        return $this->key_generator;
    }

    /**
     * Returns CallableProxy for objects method
     * @param string $method_name
     * @return CallableProxy
     */
    public function proxyGetMethodProxy($method_name)
    {
        if (isset($this->method_proxies[$method_name])) {
            return $this->method_proxies[$method_name];
        }

        $proxy = $this->proxyMakeMethodProxy($method_name);
        $this->method_proxies[$method_name] = $proxy;

        return $proxy;
    }

    /**
     * Creates CallableProxy for objects method
     * @param string $method_name
     * @return CallableProxy
     */
    protected function proxyMakeMethodProxy($method_name)
    {
        if (!$this->cache_key) {
            throw new Exceptions\Exception('Cache key is not set');
        }

        if (!method_exists($this->object, $method_name)) {
            throw new Exceptions\Exception("Method $method_name don't exist");
        }

        return new CallableProxy(
            array($this->object, $method_name),
            $this->cache_key.'.'.$method_name,
            $this->backend,
            $this->serializer,
            $this->key_generator
        );
    }
}
