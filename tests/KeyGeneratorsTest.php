<?php

namespace MZ\Proxy\Tests;

use MZ\Proxy\KeyGenerator as KeyGens;

class KeyGeneratorsTest extends \PHPUnit_Framework_TestCase
{
    protected $key_generators;

    public function setUp()
    {
        $this->key_generators = array();
        $this->key_generators['serialize'] = new KeyGens\Serialize();
    }

    public function testGenerate()
    {
        $test_object = new \stdClass();
        $test_object->attr1 = 'value';
        $test_object->attr2 = 123;
        $test_object->attr3 = array(1, 2, 3);
        $arguments = array(
            'string',
            array('array' => true),
            $test_object,
        );
        $namespace = 'test';

        foreach ($this->key_generators as $generator) {
            $key = $generator->generate($namespace, $arguments);
            $this->assertTrue(is_string($key), 'Key is string');
            $this->assertTrue(!empty($key), 'Key is not empty');
        }
    }
}
