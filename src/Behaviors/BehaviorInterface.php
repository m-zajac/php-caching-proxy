<?php

namespace MZ\Proxy\Behaviors;

/**
 * Interface for Proxy behaviors
 */
interface BehaviorInterface
{
    /**
     * Invokes callable
     * @param callable $callable
     * @param array $arguments
     * @return mixed
     */
    public function invoke($callable, array $arguments);
}
