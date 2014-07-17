<?php

namespace MZ\Proxy\Behaviors\Caching\Serializer;

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
