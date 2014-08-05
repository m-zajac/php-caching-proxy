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
     * @param Behaviors\AbstractBehavior $behavior
     * @param Serializer\SerializerInterface $serializer
     * @param KeyGenerator\KeyGeneratorInterface $key_generator
     */
    public function __construct(
        $object = null,
        Behaviors\AbstractBehavior $behavior = null
    ) {
        $this->object = $object;
        $this->behavior = $behavior;
        if ($behavior) {
            $this->behavior->setProxy($this);
        }
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

        // distinct proxy for each method?
        if ($this->behavior->distinct_for_methods) {
            $proxy = $this->proxyGetMethodProxy($name);
            return $proxy->__invoke($arguments);
        }

        // ...no, object proxy - invoke on behavior
        return $this->proxyGetBehavior()->invoke(
            array($this->object, $name),
            $arguments
        );
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
     * Returns object
     * @return object
     */
    public function proxyGetObject()
    {
        return $this->object;
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
        return $this->proxied_methods;
    }

    /**
     * Sets behavior
     * @param Behaviors\AbstractBehavior $behavior
     * @return ObjectProxy
     */
    public function proxySetBehavior(Behaviors\AbstractBehavior $behavior)
    {
        $this->behavior = $behavior;
        $this->behavior->setProxy($this);

        return $this;
    }

    /**
     * Returns behavior
     * @return Behavior\AbstractBehavior
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
     * Creates and returns CallableProxy for given method name
     * @param string $method_name
     * @return CallableProxy
     */
    protected function proxyMakeMethodProxy($method_name)
    {
        $behavior = clone $this->behavior;
        $behavior->setNamespace($behavior->getNamespace().'.'.$method_name);

        return new CallableProxy(
            array($this->object, $method_name),
            $behavior
        );
    }
}
