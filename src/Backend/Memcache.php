<?php

namespace MZ\Proxy\Backend;

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

	public function set($key, $value, $timeout = 0)
	{
		$key = $this->generateInternalKey($key);
		$timeout_time = 0;
		if ($timeout) {
			$timeout_time = time() + (int)$timeout;
		}

		$this->storage->set($key, $value, null, $timeout_time);

		return $this;
	}

    public function clear($key)
    {
    	$key = $this->generateInternalKey($key);
    	$this->storage->delete($key);

    	return $this;
    }

	public function get($key)
	{
		$key = $this->generateInternalKey($key);
		return $this->storage->get($key);
	}

	protected function generateInternalKey($key)
	{
		return 'mzp'.$this->prefix.$key;
	}
}