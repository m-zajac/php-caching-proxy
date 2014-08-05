<?php

namespace MZ\Proxy;

/**
 * Proxy wrapper for callable
 */
class CallableProxy
{
    protected $callable;
    protected $behavior;

    /**
     * Constructor
     * @param callable $callable
     * @param Behaviors\AbstractBehavior $behavior
     */
    public function __construct(
        $callable = null,
        Behaviors\AbstractBehavior $behavior = null
    ) {
        $this->callable = $callable;
        $this->behavior = $behavior;
        if ($this->behavior) {
            $this->behavior->setProxy($this);
        }
    }

    /**
     * Invokes callable
     */
    public function __invoke()
    {
        $args = func_get_args();
        return $this->behavior->invoke(
            $this->callable,
            $args
        );
    }

    /**
     * Sets callable
     * @param callable $callable
     * @return CallableProxy
     */
    public function setCallable($callable)
    {
        $this->callable = $callable;

        return $this;
    }

    /**
     * Returns callable
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * Sets behavior
     * @param Behaviors\AbstractBehavior $behavior
     * @return CallableProxy
     */
    public function setBehavior(Behaviors\AbstractBehavior $behavior)
    {
        $this->behavior = $behavior;
        if ($this->behavior) {
            $this->behavior->setProxy($this);
        }

        return $this;
    }

    /**
     * Returns behavior
     * @return Behaviors\AbstractBehavior
     */
    public function getBehavior()
    {
        return $this->behavior;
    }
}
