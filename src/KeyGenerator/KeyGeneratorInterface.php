<?php

namespace MZ\Proxy\KeyGenerator;

/**
 * Interface for Proxy key generator
 */
interface KeyGeneratorInterface
{
    /**
     * Generates key for array of arguments
     * @param string $namespace
     * @param array $arguments
     * @return string
     */
    public function generate($namespace, array $arguments);
}
