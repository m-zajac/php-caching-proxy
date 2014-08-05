<?php

namespace MZ\Proxy\Tests\Caching;

use MZ\Proxy\ObjectProxy;
use MZ\Proxy\Behaviors\Caching;

class ObjectProxyTest extends \PHPUnit_Framework_TestCase
{
    protected $proxy;
    protected $test_object;

    public function setUp()
    {
        $behavior = new Caching\CachingBehavior(
            0,
            new Caching\Backend\Memory()
        );
        $behavior->setNamespace('test_namespace');

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

    public function testCacheKeysNotOverlapping()
    {
        $this->test_object
            ->expects($this->exactly(2))
            ->method('method1')
            ->will($this->returnCallback(function () {
                return 1;
            }))
        ;
        $this->test_object
            ->expects($this->exactly(2))
            ->method('method2')
            ->will($this->returnCallback(function () {
                return 2;
            }))
        ;

        $argset_1 = array(
            123,
            new \stdClass(),
            array(1, 2, 3, 'x' => 4),
            'test'
        );
        $argset_2 = array(
            new \stdClass(),
            123,
            array(1, 2, 3, 'x' => 4),
            'test'
        );

 
        // argset 1 - fresh
        $result = call_user_func_array(array($this->proxy, 'method1'), $argset_1);
        $this->assertEquals($result, 1, 'Proxy returned valid data');

        $result = call_user_func_array(array($this->proxy, 'method2'), $argset_1);
        $this->assertEquals($result, 2, 'Proxy returned cached data');

        // argset 1 - cached, no method call
        $result = call_user_func_array(array($this->proxy, 'method1'), $argset_1);
        $this->assertEquals($result, 1, 'Proxy returned valid data');

        $result = call_user_func_array(array($this->proxy, 'method2'), $argset_1);
        $this->assertEquals($result, 2, 'Proxy returned cached data');

        // argset 2 - fresh
        $result = call_user_func_array(array($this->proxy, 'method1'), $argset_2);
        $this->assertEquals($result, 1, 'Proxy returned valid data');

        $result = call_user_func_array(array($this->proxy, 'method2'), $argset_2);
        $this->assertEquals($result, 2, 'Proxy returned valid data');

        // argset 2 - cached, no method call
        $result = call_user_func_array(array($this->proxy, 'method1'), $argset_2);
        $this->assertEquals($result, 1, 'Proxy returned valid data');

        $result = call_user_func_array(array($this->proxy, 'method2'), $argset_2);
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
