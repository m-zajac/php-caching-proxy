<?php

namespace MZ\Proxy;

/**
 * Caching proxy for objects. Caches object methods.
 * Proxy methods start with "proxy".
 */
class ObjectProxy
{
    protected $object;
    protected $behavior;
    protected $method_proxies = array();

    /**
     * If array - only methods in array will be proxied. If null - all methods proxied.
     */
    protected $proxied_methods = null;

    /**
     * Constructor
     * @param object $object
     * @param string $cache_key
     * @param Behaviors\BehaviorInterface $behavior
     * @param Serializer\SerializerInterface $serializer
     * @param KeyGenerator\KeyGeneratorInterface $key_generator
     */
    public function __construct(
        $object = null,
        Behaviors\BehaviorInterface $behavior = null
    ) {
        $this->object = $object;
        $this->behavior = $behavior;
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
     * Sets behavior
     * @param Behaviors\BehaviorInterface $behavior
     * @return ObjectProxy
     */
    public function proxySetBehavior(Behaviors\BehaviorInterface $behavior)
    {
        $this->behavior = $behavior;

        return $this;
    }

    /**
     * Returns behavior
     * @return Behavior\BehaviorInterface
     */
    public function proxyGetBehavior()
    {
        return $this->behavior;
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
        return new CallableProxy(
            array($this->object, $method_name),
            $this->behavior
        );
    }
}
