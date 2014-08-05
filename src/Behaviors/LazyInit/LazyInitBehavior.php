<?php

namespace MZ\Proxy\Behaviors\LazyInit;

use MZ\Proxy\Behaviors\AbstractBehavior;
use MZ\Proxy\CallableProxy;
use MZ\Proxy\ObjectProxy;

/**
 * LazyInitBehavior
 */
class LazyInitBehavior extends AbstractBehavior
{
    protected $loader;

    /**
     * Constructor
     * @param callable $loader
     */
    public function __construct($loader = null)
    {
        $this->loader = $loader;
    }

    /**
     * Invokes callable
     */
    public function invoke($callable, array $arguments)
    {
        if ($callable === null or (is_array($callable) and $callable[0] === null)) {
            $proxy = $this->getProxy();
            if ($proxy instanceof ObjectProxy) {
                $callable = $this->makeCallableForObjectProxy($callable);
            } elseif ($proxy instanceof CallableProxy) {
                $callable = $this->makeCallableForCallableProxy();
            } else {
                throw new \LogicException('Invalid proxy type');
            }
        }

        return call_user_func_array($callable, $arguments);
    }

    /**
     * Sets loader
     * @param callable $loader
     * @return LazyInitBehavior
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Returns loader
     * @return callable
     */
    public function getLoader()
    {
        return $this->loader;
    }

    protected function makeCallableForCallableProxy()
    {
        $subject_callable = call_user_func($this->loader);
        $proxy = $this->getProxy();
        $proxy->setCallable($subject_callable);

        return $subject_callable;
    }

    protected function makeCallableForObjectProxy($callable)
    {
        $object = call_user_func($this->loader);
        $proxy = $this->getProxy();
        $proxy->proxySetObject($object);

        $callable[0] = $object;

        return $callable;
    }
}
