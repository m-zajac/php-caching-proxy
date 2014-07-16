<?php

namespace MZ\Proxy\Serializer;

/**
 * Serialize serializer
 */
class Serialize implements SerializerInterface
{
    public function serialize($data)
    {
        return serialize($data);
    }

    public function unserialize($string)
    {
        return unserialize($string);
    }
}
