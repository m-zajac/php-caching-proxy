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
     * @param Behaviors\BehaviorInterface $behavior
     */
    public function __construct(
        $callable = null,
        Behaviors\BehaviorInterface $behavior = null
    ) {
        $this->callable = $callable;
        $this->behavior = $behavior;
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
     * @param Behaviors\BehaviorInterface $behavior
     * @return CallableProxy
     */
    public function setBehavior(Behaviors\BehaviorInterface $behavior)
    {
        $this->behavior = $behavior;

        return $this;
    }

    /**
     * Returns behavior
     * @return Behaviors\BehaviorInterface
     */
    public function getBehavior()
    {
        return $this->behavior;
    }
}
