<?php

namespace MZ\Proxy\Shortcuts;

use MZ\Proxy;

/**
 * Wraps callable or object with caching proxy with memory backend
 * @param callable|object $subject
 * @param int $timeout
 * @param \Memcache $memcache [optional]
 */
function wrapWithMemoryProxy($subject, $timeout = 0)
{
    $behavior = new Proxy\Behaviors\Caching\CachingBehavior($timeout);
    $behavior->setBackend(new Proxy\Behaviors\Caching\Backend\Memory());

    return _wrap($subject, $behavior);
}

/**
 * Wraps callable or object with caching proxy with memcache backend
 * @param callable|object $subject
 * @param int $timeout
 * @param string $cache_prefix
 * @param \Memcache $memcache [optional]
 */
function wrapWithMemcacheProxy($subject, $timeout = 0, $cache_prefix = '', \Memcache $memcache = null)
{
    $behavior = new Proxy\Behaviors\Caching\CachingBehavior($timeout);
    $behavior->setBackend(new Proxy\Behaviors\Caching\Backend\Memcache($cache_prefix, $memcache));

    return _wrap($subject, $behavior);
}

function _wrap($subject, Proxy\Behaviors\AbstractBehavior $behavior)
{
    if (is_object($subject) and !($subject instanceof \Closure)) {
        $proxy = new Proxy\ObjectProxy($subject);
        $proxy->proxySetBehavior($behavior);
    } elseif (is_callable($subject)) {
        $proxy = new Proxy\CallableProxy($subject);
        $proxy->setBehavior($behavior);
    } else {
        throw new \InvalidArgumentException('Invalid subject for proxy');
    }

    return $proxy;
}
