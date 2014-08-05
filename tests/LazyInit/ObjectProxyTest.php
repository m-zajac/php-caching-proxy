<?php

namespace MZ\Proxy\Tests\LazyInit;

use MZ\Proxy\ObjectProxy;
use MZ\Proxy\Behaviors\LazyInit;

class ObjectProxyTest extends \PHPUnit_Framework_TestCase
{
    protected $proxy;

    public function setUp()
    {
        $this->proxy = new ObjectProxy();

        $behavior = new LazyInit\LazyInitBehavior();
        $this->proxy->proxySetBehavior($behavior);
    }

    public function testProxy()
    {
        $test_object = $this->getMock('stdClass', array('method1', 'method2', '__toString'));

        $test_number = 1;
        $test_object
            ->expects($this->exactly(1))
            ->method('method1')
            ->will($this->returnCallback(function () use (&$test_number) {
                return $test_number++;
            }))
        ;
        $test_object
            ->expects($this->exactly(1))
            ->method('method2')
            ->will($this->returnCallback(function () use (&$test_number) {
                return $test_number++;
            }))
        ;

        $this->proxy->proxyGetBehavior()->setLoader(function () use ($test_object) {
            return $test_object;
        });

        $proxy = $this->proxy;
        $this->assertNull($proxy->proxyGetObject());  // no subject before first call

        $result = $proxy->method1();
        $this->assertNotNull($proxy->proxyGetObject());  // subject instantiated
        $this->assertEquals($result, 1, 'Proxy returned valid data');

        $result = $proxy->method2('arg1');
        $this->assertEquals($result, 2, 'Proxy returned valid data');
    }
}
