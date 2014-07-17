<?php

namespace MZ\Proxy\Tests\Caching;

use MZ\Proxy\ObjectProxy;
use MZ\Proxy\Behaviors\Caching;

class ObjectProxyTest extends \PHPUnit_Framework_TestCase
{
    protected $proxy;

    public function setUp()
    {
        $behavior = new Caching\CachingBehavior();
        $behavior->setBackend(new Caching\Backend\Memory());
        $behavior->setKeyGenerator(new Caching\KeyGenerator\Serialize());
        $behavior->setSerializer(new Caching\Serializer\Null());
        $behavior->setCacheKey('test_key');

        $this->test_object = $this->getMock('stdClass', array('method1', 'method2', '__toString'));

        $this->proxy = new ObjectProxy(
            $this->test_object,
            $behavior
        );
    }

    public function testProxy()
    {
        $test_number = 1;
        $this->test_object
            ->expects($this->exactly(2))
            ->method('method1')
            ->will($this->returnCallback(function () use (&$test_number) {
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
            ->will($this->returnCallback(function () use (&$test_number1) {
                return $test_number1++;
            }))
        ;

        $test_number2 = 1;
        $this->test_object
            ->expects($this->exactly(2))
            ->method('method2')
            ->will($this->returnCallback(function () use (&$test_number2) {
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
