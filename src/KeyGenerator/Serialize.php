<?php

namespace MZ\Proxy\KeyGenerator;

/**
 * Serialize key generator
 */
class Serialize implements KeyGeneratorInterface
{
    public function generate($namespace, array $arguments)
    {
        $key = '';
        foreach ($arguments as $arg) {
            $key .= serialize($arg);
        }

        return $namespace.md5($key);
    }
}
