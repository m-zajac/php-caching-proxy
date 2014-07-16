<?php

namespace MZ\Proxy;


/**
 * Proxy wrapper for callable
 */
class CallableProxy
{
	protected $cache_key;
	protected $callable;
	protected $backend;
	protected $key_generator;
	protected $serializer;
	protected $timeout = 0;

	/**
	 * Constructor
	 * @param callable $callable
	 * @param string $cache_key
	 * @param Backend\BackendInterface $backend
	 * @param Serializer\SerializerInterface $serializer
	 * @param KeyGenerator\KeyGeneratorInterface $key_generator
	 */
	public function __construct(
		$callable = null,
		$cache_key = null,
		Backend\BackendInterface $backend = null,
		Serializer\SerializerInterface $serializer = null,
		KeyGenerator\KeyGeneratorInterface $key_generator = null
	)
	{
		$this->callable = $callable;
		$this->cache_key = $cache_key;
		$this->backend = $backend;
		$this->key_generator = $key_generator;
		$this->serializer = $serializer;
	}

	/**
	 * Invokes callable
	 */
	public function __invoke()
	{
		if (!$this->cache_key) {
			throw new Exceptions\Exception('Cache key is not set');
		}

		// create cache key for args
		$args = func_get_args();
		$cache_key = $this->key_generator->generate($this->cache_key, $args);

		// try getting result from cache
		$result = $this->backend->get($cache_key);
		if ($result !== null) {
			return $this->serializer->unserialize($result);
		}

		// invoke callable
		$result = call_user_func_array($this->callable, $args);

		// store result
		$this->backend->set(
			$cache_key,
			$this->serializer->serialize($result),
			$this->timeout
		);

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
	 * Sets cache key
	 * @param string $key
	 * @return CallableProxy
	 */
	public function setCacheKey($key)
	{
		$this->cache_key = $key;

		return $this;
	}

	/**
	 * Returns cache key
	 * @return string
	 */
	public function getCacheKey()
	{
		return $this->cache_key;
	}

	/**
	 * Sets timeout
	 * @param int $value
	 * @return CallableProxy
	 */
	public function setTimeout($value)
	{
		$this->timeout = $value;

		return $this;
	}

	/**
	 * Returns timeout
	 * @return int
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}

	/**
	 * Sets backend
	 * @param Backend\BackendInterface $backend
	 * @return CallableProxy
	 */
	public function setBackend(Backend\BackendInterface $backend)
	{
		$this->backend = $backend;

		return $this;
	}

	/**
	 * Returns backend
	 * @return Backend\BackendInterface
	 */
	public function getBackend()
	{
		return $this->backend;
	}

	/**
	 * Sets serializer
	 * @param Serializer\SerializerInterface $serializer
	 * @return CallableProxy
	 */
	public function setSerializer(Serializer\SerializerInterface $serializer)
	{
		$this->serializer = $serializer;

		return $this;
	}

	/**
	 * Returns serializer
	 * @return Serializer\SerializerInterface
	 */
	public function getSerializer()
	{
		return $this->serializer;
	}

	/**
	 * Sets key generator
	 * @param KeyGenerator\KeyGeneratorInterface $key_generator
	 * @return CallableProxy
	 */
	public function setKeyGenerator(KeyGenerator\KeyGeneratorInterface $key_generator)
	{
		$this->key_generator = $key_generator;

		return $this;
	}

	/**
	 * Returns key generator
	 * @return KeyGenerator\KeyGeneratorInterface
	 */
	public function getKeyGenerator()
	{
		return $this->key_generator;
	}
}