<?php

namespace MZ\Proxy;

use Backend\BackendInterface;
use KeyGenerator\KeyGeneratorInterface;

/**
 * Proxy wrapper for callable
 */
class CallableProxy
{
	protected $callable;
	protected $backend;
	protected $key_generator;

	/**
	 * Constructor
	 * @param callable $callable
	 * @param BackendInterface $backend
	 * @param KeyGeneratorInterface $key_generator
	 */
	public function __init($callable = null, BackendInterface $backend = null, KeyGeneratorInterface $key_generator = null)
	{
		$this->callable = $callable;
		$this->backend = $backend;
		$this->key_generator = $key_generator;
	}

	/**
	 * Invokes callable
	 */
	public function __invoke()
	{
		$result = $this->callable();

		return $result;
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
	 * Sets backend
	 * @param BackendInterface $backend
	 * @return CallableProxy
	 */
	public function setBackend(BackendInterface $backend)
	{
		$this->backend = $backend;

		return $this;
	}

	/**
	 * Returns backend
	 * @return BackendInterface
	 */
	public function getBackend()
	{
		return $this->backend;
	}

	/**
	 * Sets key generator
	 * @param KeyGeneratorInterface $key_generator
	 * @return CallableProxy
	 */
	public function setKeyGenerator(KeyGeneratorInterface $key_generator)
	{
		$this->key_generator = $key_generator;

		return $this;
	}

	/**
	 * Returns key generator
	 * @return KeyGeneratorInterface
	 */
	public function getKeyGenerator()
	{
		return $this->key_generator;
	}
}