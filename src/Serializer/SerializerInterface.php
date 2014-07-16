<?php

namespace MZ\Proxy\Serializer;

/**
 * Interface for Proxy serializer
 */
interface SerializerInterface
{
    /**
     * Generates serialized string for given data
     * @param mixed $data
     * @return string
     */
    public function serialize($data);

    /**
     * Generates data back from serialized string
     * @param string $string
     * @return mixed
     */
    public function unserialize($string);
}
