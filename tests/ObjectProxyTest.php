<?php

namespace MZ\Proxy\Tests;

use MZ\Proxy;

class ObjectProxyTest extends \PHPUnit_Framework_TestCase
{
    protected $proxy;

    public function setUp()
    {
        $this->proxy = new Proxy\ObjectProxy();
        $this->proxy->proxySetBackend(new Proxy\Backend\Memory());
        $this->proxy->proxySetKeyGenerator(new Proxy\KeyGenerator\Serialize());
        $this->proxy->proxySetSerializer(new Proxy\Serializer\Null());
        $this->proxy->proxySetCacheKey('test_key');

        $this->test_object = $this->getMock('stdClass', array('method1', 'method2', '__toString'));
        $this->proxy->proxySetObject($this->test_object);
    }

    public function testProxy()
    {
        $test_number = 1;
        $this->test_object
            ->expects($this->exactly(2))
            ->method('method1')
            ->will($this->returnCallback(function() use (&$test_number) {
                return $test_number++;
            }))
        ;

        $proxy = $this->proxy;

        $result = $proxy->method1();
        $this->assertEquals($result, 1, 'Proxy returned valid data');

        $result = $proxy->method1();  // cached
        $this->assertEquals($result, 1, 'Proxy returned cached data');

        $result = $proxy->method1('arg1');
        $this->assertEquals($result, 2, 'Proxy returned fresh data for different method arguments');
    }

    public function testSelectiveMethodsProxy()
    {
        $test_number1 = 1;
        $this->test_object
            ->expects($this->exactly(1))
            ->method('method1')
            ->will($this->returnCallback(function() use (&$test_number1) {
                return $test_number1++;
            }))
        ;

        $test_number2 = 1;
        $this->test_object
            ->expects($this->exactly(2))
            ->method('method2')
            ->will($this->returnCallback(function() use (&$test_number2) {
                return $test_number2++;
            }))
        ;

        $proxy = $this->proxy;
        $proxy->proxySetMethods(array('method1'));
        $proxy->proxySetMethods('method1');  // different syntax - should work too

        $result = $proxy->method1();
        $this->assertEquals($result, 1, 'Proxy returned valid data');

        $result = $proxy->method1();  // cached
        $this->assertEquals($result, 1, 'Proxy returned cached data');

        $result = $proxy->method2();  // no cache!
        $this->assertEquals($result, 1, 'Proxy returned valid data');

        $result = $proxy->method2();  // no cache!
        $this->assertEquals($result, 2, 'Proxy returned valid data');
    }

    public function testMagicMethods()
    {
        $this->test_object
            ->expects($this->exactly(1))
            ->method('__toString')
            ->will($this->returnValue('testObject'))
        ;

        $this->proxy->test = 'test';
        $this->assertEquals($this->proxy->test, 'test', '__set, __get');
        
        $this->assertTrue(isset($this->proxy->test), '__isset');
        unset($this->proxy->test);
        $this->assertFalse(isset($this->proxy->test), '__unset');

        $this->assertEquals($this->proxy.' ok', 'testObject ok', '__toString');
    }
}
