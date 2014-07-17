<?php

namespace MZ\Proxy\Tests\Caching;

use MZ\Proxy\Behaviors\Caching;

class SerializersTest extends \PHPUnit_Framework_TestCase
{
    protected $serializers;

    public function setUp()
    {
        $this->serializers = array();
        $this->serializers['serialize'] = new Caching\Serializer\Serialize();
    }

    public function testSerialize()
    {
        $test_object = new \stdClass();
        $test_object->attr1 = 'value';
        $test_object->attr2 = 123;
        $test_object->attr3 = array(1, 2, 3);
        $data = array(
            'string',
            array('array' => true),
            $test_object,
        );
        $namespace = 'test';

        foreach ($this->serializers as $serializer) {
            $serialized = $serializer->serialize($data);
            $this->assertTrue(is_string($serialized), 'Serialized result is string');
            $this->assertTrue(!empty($serialized), 'Serialized string is not empty');

            $unserialized = $serializer->unserialize($serialized);
            $this->assertEquals($unserialized, $data, 'Unserialized data is correct');
        }
    }
}
