<?php

namespace MZ\Proxy\Serializer;

/**
 * Null serializer
 * 
 * Returns data as it is
 */
class Null implements SerializerInterface
{
    public function serialize($data)
    {
        return $data;
    }

    public function unserialize($string)
    {
        return $string;
    }
}
